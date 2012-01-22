set :application, "myeasyteam"
set :scm, "git"
set :scm_username, "doapp-jeremy"  # The server's user for deploys
set :scm_passphrase, "bond007"  # The deploy user's password
set :repository, "git@github.com:#{scm_username}/myezteam.git"  # Your clone URL
set :user, "deploy"

namespace :deploy do
  task :finalize_update, :roles => :app do
    #run "cd #{release_path}/includes/ && ln -sf ./#{stage}/config.#{stage}.php ./config.php"       
              
    # write repository revision
    write_revision
    clearobjcache
  end
end

