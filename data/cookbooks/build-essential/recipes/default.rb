#
# Cookbook Name:: build-essential
# Recipe:: default
#
# Copyright 2008-2009, Opscode, Inc.
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

require 'chef/shell_out'

case node['os']
when "linux"
  packages = value_for_platform(
    ["ubuntu", "debian"] => {
      "default" => ["build-essential", "binutils-doc"]
    },
    ["centos", "redhat", "fedora", "amazon"] => {
      "default" => ["gcc", "gcc-c++", "kernel-devel", "make"]
    }
  )

  packages.each do |pkg|
    package pkg do
      action :install
    end
  end

  %w{autoconf flex bison}.each do |pkg|
    package pkg do
      action :install
    end
  end
when "darwin"
  result = Chef::ShellOut.new("pkgutil --pkgs").run_command
  installed = result.stdout.split("\n").include?("com.apple.pkg.gcc4.2Leo")
  pkg_filename = File.basename(node['build_essential']['osx']['gcc_installer_url'])
  pkg_path = "#{Chef::Config[:file_cache_path]}/#{pkg_filename}"

  remote_file pkg_path do
    source node['build_essential']['osx']['gcc_installer_url']
    checksum node['build_essential']['osx']['gcc_installer_checksum']
    not_if { installed }
  end

  execute "sudo installer -pkg \"#{pkg_path}\" -target /" do
    not_if { installed }
  end
end
