#set :application, # this should be set in app_specific.rb
load File.join(File.dirname(__FILE__), 'app_specific.rb')
load 'config/servers.rb'

default_run_options[:pty] = true  # Must be set for the password prompt from git to work
  
set :use_sudo, false
set(:branch) { Capistrano::CLI.ui.ask("Branch to deploy (ex: master): ") }
set :git_enable_submodules, 1
set :git_submodules_recursive, 1

if ENV['localdeploy'] == '1'  
   set :scm, :none  
   set :repository, ".."  
   set :deploy_via, :copy
  
   # Setup the copy_cache dir if we're a git repo.  
   # Warning: copy_cache happens *LOCALLY* (not via SSH) when SCM=None  
   # Workaround: turn off copy_cache if it already exits  
   #             (SCM=None doesn't know how to sync)  
   if File.exists?(".git") and not(File.exists? "#{deploy_to}/shared/copy_cache")  
     set :copy_cache, "#{deploy_to}/shared/copy_cache"  
   end  
  
   # Workaround: forward_agent breaks on localhost  
   set :ssh_options, { :forward_agent => false, :keys => "/home/deploy/.ssh/deploy_rsa" }  

   # Workaround: SCP breaks when the "local" /tmp and the "remote" /tmp  
   # are the same. So override the "local" temp dir.  
   ENV['TMP'] = '/var/tmp'  
  
else  
   # This is a normal deploy.  
#  set :repository, "git@github.com:doapp/feed-cleaning.git"  # Your clone URL
#  set :scm, "git"
#  set :scm_username, "doapp-deploy"  # The server's user for deploys
#  set :scm_passphrase, "0f5F1c@-383Z"  # The deploy user's password
#  set :gateway, "ryanadmin.doapps.com:49222"
#  set :user, "deploy"
  
  ssh_options[:forward_agent] = true
  ssh_options[:keys] = "/home/jeremy/.ssh/deploy_rsa" 
  ssh_options[:auth_methods] = "publickey"
  ssh_options[:config]=false
  #ssh_options[:verbose] = :debug
   
  set :keep_releases, 2
  set :deploy_via, :remote_cache
  #set :repository_cache, "git_cache"
end  

set :copy_exclude, ["/deploy","/docs","/deploy_auto", "s3"]

# Environments 
task :test_env do 
  set :stage, "test"
  set :deploy_to, "/opt/#{application}/#{stage}"
end 
task :staging do 
  set :stage, "staging"
  set :deploy_to, "/opt/#{application}/#{stage}"
end 
task :production do 
  set :stage, "prod"
  set :deploy_to, "/opt/#{application}/#{stage}"
end 

namespace :deploy do
  task :default, :except => { :no_release => true } do
    from = source.next_revision(current_revision)
    system(source.local.log(from))
  end
  # finalize_update should be in app_specific (if needed)
  #task :finalize_update, :roles => :app do
  #end
end

task :clearobjcache, :roles => :app do
  #clear the APC user/system/opcode cache.
  run "wget --no-check-certificate -qO- http://localhost:49000/clearapc.php?apcpw=clear4DaCache"
end

task :write_revision do
  #set :revision_php, "<?php class SVN { const REPOSITORY = '#{repository}'; const REVISION = '#{real_revision}'; } ?>"
  #run "echo \"#{revision_php}\" > #{release_path}/revision.php"
end
