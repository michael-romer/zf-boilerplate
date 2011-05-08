<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    public function _initAutoloaderNamespaces()
    {
        require_once APPLICATION_PATH .
            '/../library/Doctrine/Common/ClassLoader.php';

        $autoloader = \Zend_Loader_Autoloader::getInstance();
        $fmmAutoloader = new \Doctrine\Common\ClassLoader('Bisna');

        $autoloader->pushAutoloader(
            array($fmmAutoloader, 'loadClass'),
            'Bisna'
        );
        
        $fmmAutoloader = new \Doctrine\Common\ClassLoader('App');
        $autoloader->pushAutoloader(array($fmmAutoloader, 'loadClass'), 'App');
        $fmmAutoloader = new \Doctrine\Common\ClassLoader('Boilerplate');

        $autoloader->pushAutoloader(
            array($fmmAutoloader, 'loadClass'),
            'Boilerplate'
        );

        require_once APPLICATION_PATH .
            '/../library/Symfony/Component/Di/sfServiceContainerAutoloader.php';

        sfServiceContainerAutoloader::register();
    }

    public function _initModuleLayout()
    {
        $front = Zend_Controller_Front::getInstance();

        $front->registerPlugin(
            new Boilerplate_Controller_Plugin_ModuleLayout()
        );
        
        $front->setParam('prefixDefaultModule', true);
        $eh = new Zend_Controller_Plugin_ErrorHandler();
        $front = Zend_Controller_Front::getInstance()->registerPlugin($eh);
    }

    public function _initServices()
    {
        $sc = new sfServiceContainerBuilder();
        $loader = new sfServiceContainerLoaderFileXml($sc);
        $loader->load(APPLICATION_PATH . "/configs/services.xml");
        Zend_Registry::set('sc', $sc);
    }

}