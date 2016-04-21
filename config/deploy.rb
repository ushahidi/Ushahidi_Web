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

  after :restart, :clear_cache do
    on roles(:web), in: :groups, limit: 3, wait: 10 do
      within release_path do
        execute :rm, ' -r installer/ '
      end
    end
  end

end
