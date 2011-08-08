<?php
// Define path to application directory
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));

// Define application environment
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'development'));

// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
    realpath(APPLICATION_PATH . '/../library'),
    get_include_path(),
)));

include "Zend/Loader/Autoloader.php";
$l = Zend_Loader_Autoloader::getInstance();
$l->registerNamespace('Boilerplate_');

// Making XDebug more chatty in Development Environment
if (APPLICATION_ENV == 'development')
{
    ini_set('xdebug.collect_vars', 'on');
    ini_set('xdebug.collect_params', '4');
    ini_set('xdebug.dump_globals', 'on');
    ini_set('xdebug.dump.SERVER', 'REQUEST_URI');
    ini_set('xdebug.show_local_vars', 'on');
}

// Create application, bootstrap, and run
$application = new Zend_Application(
    APPLICATION_ENV,
    APPLICATION_PATH . '/configs/application.ini'
);

$application->bootstrap()
            ->run();