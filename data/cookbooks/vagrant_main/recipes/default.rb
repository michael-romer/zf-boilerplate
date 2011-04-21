require_recipe "apt"
require_recipe "apache2"
require_recipe "mysql::server"
require_recipe "php::php5"
require_recipe "elasticsearch"
require_recipe "ant"

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
