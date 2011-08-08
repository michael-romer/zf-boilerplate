#
# Author::  Joshua Timberman (<joshua@opscode.com>)
# Cookbook Name:: php
# Recipe:: pear
#
# Copyright 2009, Opscode, Inc.
#
# Licensed under the Apache License, Version 2.0 (the "License");
# you may not use this file except in compliance with the License.
# You may obtain a copy of the License at
#
#     http://www.apache.org/licenses/LICENSE-2.0
#
# Unless required by applicable law or agreed to in writing, software
# distributed under the License is distributed on an "AS IS" BASIS,
# WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
# See the License for the specific language governing permissions and
# limitations under the License.
#

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

# using apt
package "phpunit" do
  action :install
end

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