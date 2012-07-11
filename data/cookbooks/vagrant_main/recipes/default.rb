require_recipe "apt"
require_recipe "build-essential"
require_recipe "networking_basic"
require_recipe "apache2"
include_recipe "apache2::mod_php5"
include_recipe "apache2::mod_rewrite"
include_recipe "apache2::mod_deflate"
include_recipe "apache2::mod_headers"
require_recipe "mysql::server"
require_recipe "vagrant_main::custom_php"
require_recipe "elasticsearch"
require_recipe "ant"
require_recipe "memcached"

# Install mysql gem
gem_package "mysql" do
  action :install
end

ruby_block "Create database + execute grants" do
  block do
    require 'rubygems'
    Gem.clear_paths
    require 'mysql'
    m = Mysql.new("localhost", "root", "")
    m.query("CREATE DATABASE IF NOT EXISTS app CHARACTER SET utf8")
    m.reload
  end
end

# Initialize web app
web_app "default" do
    template "default.conf.erb"
    server_name "localhost"
    server_aliases [node['fqdn'], "localhost"]
    docroot "#{node[:vagrant][:directory]}/public"
end