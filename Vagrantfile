Vagrant::Config.run do |config|
  config.vm.box = "lucid32"
  config.vm.forward_port "http", 80, 8080
  config.vm.provision :chef_solo do |chef|
     chef.cookbooks_path = "data/cookbooks"
     chef.add_recipe("vagrant_main")
     chef.log_level = :debug
     chef.json.merge!({ :mysql => { :server_root_password => "" } })
  end
end