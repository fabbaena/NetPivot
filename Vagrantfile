# -*- mode: ruby -*-
# vi: set ft=ruby :

Vagrant.require_version ">= 1.8.0"

Vagrant.configure("2") do |config|
    config.vm.box = "ubuntu/trusty64"
    config.vm.box_check_update = true
    config.vm.communicator = "ssh"
    config.vm.guest = :linux
    config.vm.network "forwarded_port", guest: 80, host: 8080, nic_type: "virtio"
    config.vm.provider "virtualbox" do |vbox|
	vbox.gui = false
	vbox.name = "netpivot-develop"
	vbox.memory = 768
	vbox.cpus = 1
    end
    #config.vm.provision "file"
    config.vm.provision "shell", path: "scripts/vagrant.sh"
    config.vm.synced_folder "content/", "/var/www/html", create: true, group: "www-data", owner: "www-data", id: "content"

    if Vagrant.has_plugin?("vagrant-proxyconf")
	config.proxy.http = "http://192.168.1.1:3128"
	config.proxy.https = "http://192.168.1.1:3128"
	config.proxy.ftp = "http://192.168.1.1.:3128"
	config.proxy.no_proxy = "localhost,127.0.0.1"
    end
end

