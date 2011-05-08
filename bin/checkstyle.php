<?php
$folders = array('../library/App', '../application');
$path = implode(' ', $folders);
echo shell_exec('phpcs -v --standard=zend ' . $path) . PHP_EOL;