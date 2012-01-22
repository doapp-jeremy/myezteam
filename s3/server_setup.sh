#!/bin/bash

APP_NAME="myeasyteam"
APP_STAGING_ROOT="/opt/$APP_NAME/staging"
APP_PROD_ROOT="/opt/$APP_NAME/prod/"

#myeasyteam common server setup

if [[ $EUID -ne 0 ]]; then
   echo "This script must be run as root" 1>&2
   exit 1
fi

#we are in amazon so we know the ip and hostname
PRIVATE_IP=`/sbin/ifconfig eth0 | grep 'inet addr:' | cut -d: -f2 | awk '{ print $1}'`
PRIVATE_FQDN=`/bin/hostname -f`

mkdir -p /var/www

# enable multi-verse first: https://help.ubuntu.com/community/Repositories/CommandLine#Adding%20the%20Universe%20and%20Multiverse%20Repositories

sudo add-apt-repository ppa:nginx/stable
sudo apt-get update
sudo apt-get -y upgrade
apt-get update && apt-get dist-upgrade

sudo apt-get install --assume-yes --force-yes ec2-api-tools php-apc php5-memcached php5-memcache php5-cli php5-dev php5-common php5-curl php5-mcrypt php5-mysql php-pear subversion php5-suhosin ec2-api-tools nginx-full php5-fpm capistrano rubygems rubygems1.9.1 rubygems1.8 libpcre3-dev git postifx monit

yes yes | sudo pecl install memcache-3.0.6

sudo pecl install apc-3.1.9

gem install railsless-deploy

#reboot here is not a bad idea

adduser deploy www-data
adduser deploy admin

#prevent /var/log/syslog certificate verification failed for smtp.gmail.com errors
cat /etc/ssl/certs/Thawte_Premium_Server_CA.pem | sudo tee -a /etc/postfix/cacert.pem
cat /etc/ssl/certs/Equifax_Secure_CA.pem >> /etc/postfix/cacert.pem
/etc/init.d/postfix restart

# don't need moxi, will use memcache on server
#install moxi
#cd /tmp && wget http://packages.couchbase.com/releases/1.7.1/moxi-server_x86_1.7.1.deb
#dpkg -i moxi-server_x86_1.7.1.deb
#rm moxi-server_x86_1.7.1.deb
#adduser dasdeploy moxi

echo "
apc.stat = 0
apc.stat_ctime = 0
" > /etc/php5/fpm/conf.d/production.ini

#update fpm www pool
FPMWWW_POOL=/etc/php5/fpm/pool.d/www.conf

sed -i -e "s|^listen =.*$|listen = /tmp/php-fastcgi.sock|g" $FPMWWW_POOL
sed -i -e "s/^pm\.max_children =.*$/pm\.max_children = 100/g" $FPMWWW_POOL
sed -i -e "s/^pm\.min_spare_servers =.*$/pm\.min_spare_servers = 10/g" $FPMWWW_POOL
sed -i -e "s/^;pm\.max_requests =.*$/pm\.max_requests = 500/g" $FPMWWW_POOL

CLEAR_APC_ROOT="/opt/webtools"
echo "#this server is to clear PHP APC
server {
	listen 127.0.0.1:49000;

	root        $CLEAR_APC_ROOT;
	index       index.php index.html;	

    # Pass the PHP scripts to FastCGI server
    # listening on 127.0.0.1:9000
    location ~ \.php$ {
	fastcgi_pass   127.0.0.1:9000;
        #fastcgi_pass   127.0.0.1:9000;
        fastcgi_index  index.php;
        fastcgi_intercept_errors on; # to support 404s for PHP files not found
        include fastcgi_params;
    }
}" > /etc/nginx/sites-available/clearapc

mkdir /opt/webtools
cat << 'EOF' > "$CLEAR_APC_ROOT/clearapc.php"
<?php
if( isset($_GET['apcpw']) && $_GET['apcpw'] == 'clear4DaCache'){
//We have to clear the APC cache this way, cuz cant do it via command line.     
        echo "Starting APC CLEAR: ".date(DATE_RFC822)."\n";
        set_time_limit(0);
        apc_clear_cache();
        apc_clear_cache('user');
        apc_clear_cache('opcode');      
        echo "APC DONE: ".date(DATE_RFC822)."\n";
}
?>
EOF

rm /etc/nginx/sites-enabled/default > /dev/null 2>&1

#Now setup log rotation for cake logs
# have to set thes STAGE variable because you can't do something like $APP_NAME_staging
STAGE="staging"
echo "$APP_STAGING_ROOT/current/app/tmp/logs/*.log {
	daily
	rotate 6
	compress
	delaycompress
	missingok
	notifempty
	copytruncate
	nomail
}" > "/etc/logrotate.d/$APP_NAME$STAGE"
STAGE="_prod"
echo "$APP_PROD_ROOT/current/app/tmp/logs/*.log {
	daily
	rotate 6
	compress
	delaycompress
	missingok
	notifempty
	copytruncate
	nomail
}" > "/etc/logrotate.d/$APP_NAME$STAGE"

echo "/var/log/php5-fpm.log {
        daily
        rotate 6
        compress
	delaycompress
        missingok
        notifempty
        copytruncate
        nomail
}

/var/log/php-fpm.log.slow {
        daily
        rotate 6
        compress
	delaycompress
        missingok
        notifempty
        copytruncate
        nomail
}" > /etc/logrotate.d/php5-fpm

#now setup production and staging apache symlinks (conf files wont exist til you deploy the app though, but that is ok)
ln -s "$APP_STAGING_ROOT/current/etc/nginx.staging.conf" "/etc/nginx/sites-enabled/$APP_NAME_staging"  > /dev/null 2>&1
ln -s "$APP_PROD_ROOT/current/etc/nginx.prod.conf" "/etc/nginx/sites-enabled/$APP_NAME_prod"  > /dev/null 2>&1
ln -s /etc/nginx/sites-available/clearapc /etc/nginx/sites-enabled/clearapc > /dev/null 2>&1

#make the default code dirs
sudo mkdir -p "$APP_STAGING_ROOT"
sudo mkdir -p "$APP_PROD_ROOT"

sudo mkdir -p /opt/cake/runtime

mkdir -p /opt/cake/runtime/13  > /dev/null 2>&1
cd /opt/cake/runtime/13
ln -s /opt/cake/runtime/13/139 /opt/cake/runtime/13/latest  > /dev/null 2>&1

chown -R deploy:admin /opt/$APP_NAME
chown -R deploy:admin /opt/cake/runtime


update-rc.d nginx defaults > /dev/null 2>&1

cat << 'EOF' > /etc/monit/conf.d/php-fpm.monitrc 
check process php5-fpm
    with pidfile /var/run/php5-fpm.pid
    start program = "/etc/init.d/php5-fpm start"
    stop program =  "/etc/init.d/php5-fpm stop"

EOF

/etc/init.d/monit restart
/etc/init.d/nginx restart

#setup permissions for cutycapt (if we ever need flash)
#sudo mkdir /var/www/.kde
#sudo chmod 777 /var/www/.kde/
#sudo chown www-data /var/www/.kde/
#sudo mkdir /var/www/.adobe
#sudo chmod 777 /var/www/.adobe/
#sudo chown www-data /var/www/.adobe/

#update sudoers
if [ ! -f "/etc/sudoers.tmp" ]; then
	touch /etc/sudoers.tmp
cat << 'EOF' > /tmp/sudoers.new
# /etc/sudoers
# This file MUST be edited with the 'visudo' command as root.
# See the man page for details on how to write a sudoers file.

Defaults	env_reset

# Host alias specification

# User alias specification

# Cmnd alias specification

# User privilege specification
root	ALL=(ALL) ALL

# Uncomment to allow members of group sudo to not need a password
# (Note that later entries override this, so you might need to move
# it further down)
# %sudo ALL=NOPASSWD: ALL

# Members of the admin group may gain root privileges
%admin ALL=(ALL) ALL

# ubuntu user is default user in ec2-images.  
# It needs passwordless sudo functionality.
ubuntu  ALL=(ALL) NOPASSWD:ALL

User_Alias DEPLOYERS = deploy
# Cmnd alias specification
Cmnd_Alias DEPLOY = /usr/bin/nohup, /usr/sbin/nginx, /etc/init.d/nginx, /etc/init.d/php5-fpm, /usr/sbin/php5-fpm, /etc/init.d/moxi-server, /opt/moxi/etc/moxi-init.d, /opt/moxi/bin/moxi

# User privilege specification
DEPLOYERS ALL = NOPASSWD: DEPLOY

EOF

	visudo -c -f /tmp/sudoers.new
	if [ "$?" -eq "0" ]; then
	    cp /tmp/sudoers.new /etc/sudoers
	fi
	rm /etc/sudoers.tmp	
fi

#so we can deploy via cap to a diff ssh port:
mkdir -p /home/deploy/.subversion
chown deploy:deploy /home/deploy/.subversion
cat << 'EOF' > /home/deploy/.subversion/config
[auth]

[helpers]

[miscellany]

[auto-props]

[tunnels]
sshtunnel = ssh -p 49222

EOF

cat << 'EOF' > /var/www/elb_status.html
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>working</title>
</head>
<body>works
</body>
</html>

EOF

cat << 'EOF' > /etc/nginx/sites-available/elb_status
server {
listen 81;

root        /var/www;
index       index.php index.html;

access_log off;
error_log off;
rewrite_log off;

    # Pass the PHP scripts to FastCGI server
    # listening on 127.0.0.1:9000
    location ~ \.php$ {
        fastcgi_pass   127.0.0.1:9000;        
        fastcgi_index  index.php;
        fastcgi_intercept_errors on; # to support 404s for PHP files not found
        include	fastcgi_params;
    }
}

EOF

cat << 'EOF' > /etc/nginx/nginx.conf
user www-data;
worker_processes 4;
pid /var/run/nginx.pid;

events {
#	worker_connections 768;
	worker_connections 1024;
	# multi_accept on;
	use epoll;
}

http {

	##
	# Basic Settings
	##
	charset utf-8;
	server_tokens off;
	sendfile on;
	tcp_nopush on;
	tcp_nodelay on;
	keepalive_timeout 65;
	types_hash_max_size 2048;	

	include /etc/nginx/mime.types;
	default_type application/octet-stream;

	access_log /var/log/nginx/access.log;
	error_log /var/log/nginx/error.log;

	gzip on;
	gzip_disable "msie6";

	include /etc/nginx/conf.d/*.conf;
	include /etc/nginx/sites-enabled/*;
}
EOF

#make it so ssh never fails with unknown host
cat << 'EOF' > /home/deploy/.ssh/config
Host ec2*.compute-1.amazonaws.com
    IdentityFile /home/deploy/.ssh/deploy_rsa
    Compression yes
    Cipher blowfish
    Protocol 2
    User deploy
    UserKnownHostsFile=/dev/null
    StrictHostKeyChecking no
    Port 49222 

Host github.com 
    HostName github.com
    User git
    IdentityFile /home/deploy/.ssh/deploy_rsa
    Protocol 2
    StrictHostKeyChecking no
    UserKnownHostsFile=/dev/null  

EOF

#setup 2 moxi servers
#cp /etc/init.d/moxi-server /etc/init.d/das-moxi-server
#cp /opt/moxi/etc/moxi-init.d /opt/moxi/etc/moxi-init-das.d
#cp /opt/moxi/etc/moxi.cfg /opt/moxi/etc/moxi-das.cfg
#cp /opt/moxi/etc/moxi-cluster.cfg /opt/moxi/etc/moxi-cluster-das.cfg

#sed -i -e "s|^exec /opt/moxi/etc/moxi-init.d|exec /opt/moxi/etc/moxi-init-das.d|g" /etc/init.d/das-moxi-server
#sed -i -e "s|^port_listen=11211,|port_listen=11212,|g" /opt/moxi/etc/moxi-das.cfg
#sed -i -e "s|^PIDFILE=/var/run/moxi-server.pid|PIDFILE=/var/run/das-moxi-server.pid|g" /opt/moxi/etc/moxi-init-das.d
#sed -i -e "s|^MOXI_CFG=/opt/moxi/etc/moxi.cfg|MOXI_CFG=/opt/moxi/etc/moxi-das.cfg|g" /opt/moxi/etc/moxi-init-das.d
#sed -i -e "s|^MOXI_CLUSTER_CFG=/opt/moxi/etc/moxi-cluster.cfg|MOXI_CLUSTER_CFG=/opt/moxi/etc/moxi-cluster-das.cfg|g" /opt/moxi/etc/moxi-init-das.d

grep /ramdrive /etc/fstab  > /dev/null 2>&1
if [ $? != 0 ]; then
	mkdir /ramdrive
	chmod 777 /ramdrive
	echo "tmpfs /ramdrive tmpfs size=50M,mode=0777 0 0" >> /etc/fstab
fi

echo "Setup github access: http://help.github.com/linux-set-up-git/"

echo "DONE. Restart instance, then make AMI"

