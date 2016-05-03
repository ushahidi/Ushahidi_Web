# -*- mode: ruby -*-
# vi: set ft=ruby :

Vagrant.configure(2) do |config|
  config.vm.box = "ubuntu/precise64"
  config.vm.network "forwarded_port", guest: 80, host: 8080
  config.vm.synced_folder ".", "/vagrant", owner: "www-data", group: "www-data"
  config.vm.provision :shell, :path => 'scripts/bootstrap.sh'
end
