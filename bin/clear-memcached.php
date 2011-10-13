<?php
require_once('bootstrap.php');
// FIX ME: Not cross-platform compatible hack
echo exec('sudo service memcached restart') . PHP_EOL;
echo "Memcached succesfully cleared!" . PHP_EOL;