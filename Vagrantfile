Vagrant::Config.run do |config|
  config.vm.customize [
    "modifyvm", :id,
    "--memory", "1024",
    "--cpus", "2"
  ]
  config.vm.box = "precise64"
  config.vm.forward_port 80, 8080
  config.vm.forward_port 3306, 3306
  config.ssh.max_tries = 50
  config.ssh.timeout   = 300
  config.vm.provision :chef_solo do |chef|
     chef.cookbooks_path = "data/cookbooks"
     chef.add_recipe("vagrant_main")
     chef.log_level = :debug
     # Configure http(s) proxy if needed (requires Vagrant >= 0.7.4)
     # chef.http_proxy = "http://proxy.vmware.com:3128"
     # chef.https_proxy = "http://proxy.vmware.com:3128"
     # chef.http_proxy_user = "my_username"
     # chef.http_proxy_pass = "foo"
     chef.json.merge!({ :mysql => { :server_root_password => "" } })
  end
end
