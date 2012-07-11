require_recipe "php"
require_recipe "php::module_mysql"
require_recipe "php::module_apc"
require_recipe "php::module_memcache"
require_recipe "php::module_curl"

package "php-pear" do
  action :install
end

php_pear_channel "pear.phpunit.de" do
  action :discover
end

php_pear_channel "components.ez.no" do
  action :discover
end

php_pear_channel "pear.symfony-project.com" do
  action :discover
end

php_pear_channel "pear.phpmd.org" do
  action :discover
end

php_pear_channel "pear.pdepend.org" do
  action :discover
end

php_pear_channel "pear.docblox-project.org" do
  action :discover
end

php_pear_channel "pear.michelf.com" do
  action :discover
end

# using apt
package "phpunit" do
  action :install
end

# XSL needed by DocBlox
package "php5-xsl" do
  action :install
end

# Graphviz needed by DocBlox
package "graphviz" do
  action :install
end

# Sqlite needed by PHD (Docbook)
package "php5-sqlite" do
  action :install
end


# Using PEAR installer

execute "PEAR: upgrade all packages" do
  command "pear upgrade-all"
end

execute "PEAR: install phpmd/PHP_PMD" do
  command "pear install -f phpmd/PHP_PMD"
end

execute "PEAR: install pdepend/PHP_Depend" do
  command "pear install -f pdepend/PHP_Depend"
end

execute "PEAR: install PHP_CodeSniffer-1.3.0" do
  command "pear install -f PHP_CodeSniffer-1.3.0"
end

execute "PEAR: install phploc-1.5.0" do
  command "pear install -f phpunit/phploc"
end

execute "PECL: install xdebug" do
  command "pecl install xdebug"
end

execute "PEAR: install phpcpd" do
  command "pear install -f phpunit/phpcpd"
end

execute "PEAR: install docblox" do
  command "pear install -f docblox/DocBlox"
end

execute "PEAR: install phd" do
  command "pear install -f --alldeps doc.php.net/phd"
end


# Install xDebug
php_pear "xdebug" do
  # Specify that xdebug.so must be loaded as a zend extension
  zend_extensions ['xdebug.so']
  action :install
end

# Install the php packages we need
## Upgrade existing packages
execute "PEAR: upgrade all packages" do
  command "pear upgrade-all"
end