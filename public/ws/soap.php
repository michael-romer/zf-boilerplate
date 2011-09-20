<?php
// Define path to application directory
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../../application'));

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
include "App/Webservice/Calls.php";

// Create application, bootstrap, and run
$application = new Zend_Application(
    APPLICATION_ENV,
    APPLICATION_PATH . '/configs/application.ini'
);

$application->bootstrap();

if (isset($_GET['WSDL'])) {
	$autodiscover = new \Boilerplate\Webservice\Soap\AutoDiscover();
	$autodiscover->setUri('http://' . $_SERVER['SERVER_NAME'] . ':' . $_SERVER['SERVER_PORT'] . '/ws/soap.php');
	$autodiscover->setClass("\\App\\Webservice\\Calls");
	$autodiscover->handle();
} elseif (isset($_GET['INTERNALWSDL'])) {
    $autodiscover = new Zend_Soap_AutoDiscover();
    $autodiscover->setClass('\\App\\Webservice\\Calls');
    $autodiscover->setUri('http://' . $_SERVER['SERVER_NAME'] . ':' . $_SERVER['SERVER_PORT'] . '/ws/soap.php');
    $autodiscover->handle();
} else {
    $options = array('soap_version' => SOAP_1_2);
    $server = new \Boilerplate\Webservice\Soap\Server('http://'.$_SERVER['SERVER_NAME'].'/ws/soap.php?INTERNALWSDL=1', $options);
    $server->setObject(new \App\Webservice\Calls());
    $server->handle();
}