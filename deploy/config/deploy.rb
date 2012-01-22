load '../deploy_auto/config/deploy.rb'

# before updating, delete remove cache, this will allow us to change tags without having delete manually
# it'll take a littler longer each time to update, but that extra time is insignificant
before "deploy:update", "delete_remote_cache"

# clean up old releases  
after "deploy:update", "deploy:cleanup"

task :reload_php do
  run "sudo /etc/init.d/php5-fpm reload"
end

#use reload unless you really need to restart
task :restart_php do
  run "#{current_path}/etc/restartPHPFpm.sh"
end

task :restart_nginx do
  run "sudo /etc/init.d/nginx reload"
end

task :clear_memcache do
  run "echo 'flush_all' | nc localhost 11211"
end

task :restart_moxi do
  run "sudo /etc/init.d/moxi-server restart"
end

desc "clear all cached SVN/Cap copys"
task :delete_remote_cache, :roles => :app do  
  run "rm -rf #{shared_path}/cached-copy"  
end  
