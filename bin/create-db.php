<?php
require_once('bootstrap.php');
echo exec('php doctrine.php orm:schema-tool:drop --force') . PHP_EOL;
echo exec('php doctrine.php orm:schema-tool:create') . PHP_EOL;