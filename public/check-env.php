<?php

require_once("../tests/bootstrap.php");

// this param is mandatory, others are optional
$phpRackConfig = array(
    'dir' => '../tests/rack-tests',
);
// absolute path to the bootstrap script on your server
include '../library/phpRack/bootstrap.php';