<?php
require_once('bootstrap.php');

define('INDEX_LABEL', 'quotes');

$client = Zend_Registry::get('es');

try {
    $index = $client->getIndex(INDEX_LABEL);
    $index->delete(array(), true);
    echo "Index " . INDEX_LABEL . " successfully dropped." . PHP_EOL;
} catch (Exception $e) {
    echo "Index " . INDEX_LABEL . " could not dropped (maybe already gone?)" . PHP_EOL;
}

try {
    $index = $client->getIndex(INDEX_LABEL);
    $index->create(array(), true);
    echo "Index " . INDEX_LABEL . " successfully created." . PHP_EOL;
} catch (Exception $e) {
    echo "Index " . INDEX_LABEL . " could not be created." . PHP_EOL;
}