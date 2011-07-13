<?php

class Boilerplate_Resource_Depinjector
    extends Zend_Application_Resource_ResourceAbstract
{

    public function init()
    {
        return $this->getDependencyInjector();
    }

    public function getDependencyInjector()
    {
        $dependencyInjector =
                new Boilerplate_Controller_Helper_DependencyInjector();

        Zend_Controller_Action_HelperBroker::addHelper($dependencyInjector);
    }
}