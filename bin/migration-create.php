<?php
require_once('bootstrap.php');
passthru('php doctrine.php migration:diff') . PHP_EOL;