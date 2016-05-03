# config valid only for current version of Capistrano
lock '3.4.1'

set :application, 'mapa_desastre_ec'
set :repo_url, 'git@github.com:desastre-ecuador/mapa.desastre.ec.git'
set :scm, :git
set :format, :pretty
set :log_level, :debug
set :pty, true
set :keep_releases, 5

server 'mapa.desastre.ec', user: 'deployer', roles: %{app}
role :app, %w{deployer@mapa.desastre.ec}

set :ssh_options, {
    keys: %w(config/deploy/id_rsa_deploy ~/.ssh/id_rsa),
    forward_agent: false,
    auth_methods: %w(publickey),
    user: 'deployer',
    port: 2231,
}
set :linked_files, %w{
  .htaccess
  application/config/auth.php
  application/config/config.php
  application/config/database.php
  application/config/encryption.php
}
set :linked_dirs, %w{application/logs application/cache media/uploads}

namespace :deploy do
  desc "Clean installer and cache"
  task :clean_install do
    on roles(:app) do
      within release_path do
        execute :sudo, ' rm -rf installer/ application/cache/*'
        execute :sudo, ' service apache2 reload'
      end
    end
  end
end

namespace :db do
  desc "Make a database dump"
  task :dump do
    on roles(:app) do |host|
      db_name = fetch(:db_name)
      ask(:db_password, db_name, echo: false)
      file_name = "/tmp/#{Time.now.to_i}.#{db_name}.sql.gz"
      execute "set -o pipefail && mysqldump --force --opt -u #{db_name} -p --databases #{db_name} | gzip -9 > #{file_name}",
        interaction_handler: { /Enter password/ => "#{fetch(:db_password)}\n" }
      puts "Database dumped in #{file_name} in the remote host"
    end
  end
end

after 'deploy:finished', 'deploy:clean_install'
