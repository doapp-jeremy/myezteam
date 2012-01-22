#!/bin/bash

# NOTE: this script needs to be run as dasdeploy!!

# This script does a deploy of mln ad serving instance. Meant to be called as a new instance is coming up.
#
# This script expects the 2 following params to be passed
# 1. INTERNAL IP of instance we are deploying to (ex: 10.x)
# 2. SVN tag to deploy. Ex: trunk
#

EXPECTED_ARGS=1
APP_NAME=myeasyteam
DIRECTORY=/tmp/myeasyteam/apps/deploy/$APP_NAME
LOGFILE=$DIRECTORY/deploy.log
GIT_REMOTE="git@github.com:doapp-jeremy/myezteam.git"

BRANCH="master"

test=`whoami`
case "$test" in
        "deploy" )
		mkdir -p "$DIRECTORY"
		date > $LOGFILE
		chmod a+rw $LOGFILE
        ;;
        *)
                echo "must be user deploy"
                exit 1
        ;;
esac

if [ $# -ne $EXPECTED_ARGS ]
then
  echo "invalid number of args. number sent: $#" >> $LOGFILE
  exit $E_BADARGS
fi

#the ip to deploy the code to. Must be the internal ip!!
DEPLOY_IP=$1

cd $DIRECTORY >> $LOGFILE 2>&1

function setupdeploy {
	git clone --recursive $GIT_REMOTE >> $LOGFILE 2>&1
	cd $APP_NAME >> $LOGFILE 2>&1
	git checkout $BRANCH >> $LOGFILE 2>&1
	git submodule init >> $LOGFILE 2>&1
	git submodule update --recursive >> $LOGFILE 2>&1
	cd deploy_auto/ >> $LOGFILE 2>&1

	#setup deploy.rp to deploly to the instance ip
	echo "server \"$DEPLOY_IP:49222\", :app" > config/servers.rb
}  

#NOTE: this you may have to modify to set any vars you need for your cap script
setupdeploy
echo "deploying production" >> $LOGFILE 2>&1
cap localdeploy=1 production deploy:setup -s branch="$BRANCH" >> $LOGFILE 2>&1
cap localdeploy=1 production deploy:update -s branch="$BRANCH" >> $LOGFILE 2>&1

echo "deploying staging" >> $LOGFILE 2>&1
cap localdeploy=1 staging deploy:setup -s branch="$BRANCH" >> $LOGFILE 2>&1
cap localdeploy=1 staging deploy:update -s branch="$BRANCH" >> $LOGFILE 2>&1

for i in `seq 1 10`;
do
	#now restart nginx and php5-fpm
	sudo /etc/init.d/php5-fpm restart
	sleep 5
	netstat -ltn | grep :9000 >> $LOGFILE 2>&1
	if [ $? -eq 0 ]; then
		echo "FPM is running" >> $LOGFILE 2>&1
		break 
	else
		echo "FPM is NOT running, trying again" >> $LOGFILE 2>&1   
	fi
done

sudo /etc/init.d/nginx stop
sudo /etc/init.d/nginx start
date >> $LOGFILE

