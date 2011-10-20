<?php
require_once('bootstrap.php');
passthru('php doctrine.php migration:status') . PHP_EOL;