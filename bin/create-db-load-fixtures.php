<?php
require_once('bootstrap.php');
echo exec('php doctrine.php orm:schema-tool:drop --force') . PHP_EOL;
echo exec('php doctrine.php orm:schema-tool:create') . PHP_EOL;
$container = $application->getBootstrap()->getResource('doctrine');
$em = $container->getEntityManager();
echo "Database fixtures loaded successfully!";
require_once('../data/fixtures/fixtures.php');