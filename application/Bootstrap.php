<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    public function _initConfig()
    {
        Zend_Registry::set('config', $this->getOptions());
    }
    
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

    public function _initLocale()
    {
        try{
            $locale = new Zend_Locale(Zend_Locale::BROWSER);
        } catch (Zend_Locale_Exception $e) {
            $config = $this->getOptions();
            $locale = new Zend_Locale($config['resources']['locale']['default']);
        }

        //         Zend_Locale::setDefault('de'); // check hasLanguage, sonst switch auf default siehe Zend TIcket

        Zend_Registry::set('Zend_Locale', $locale);

        $translator = new Zend_Translate(
            array(
                'adapter' => 'Csv',
                'content' => APPLICATION_PATH . '/../data/lang/',
                'scan' => Zend_Translate::LOCALE_DIRECTORY,
                'delimiter' => ',',
                'disableNotices' => true,
            )
        );

        Zend_Registry::set('Zend_Translate', $translator);
        Zend_Form::setDefaultTranslator($translator);
    }

    public function _initElasticSearch()
    {
        $es = new Elastica_Client();
        Zend_Registry::set('es', $es);
    }

}