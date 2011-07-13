<?php

class Ctrlr_Resource_DepInjector extends Zend_Application_Resource_ResourceAbstract
{

	public function init()
	{
		return $this->getDepInjector();
	}

	public function getDepInjector()
	{
            require "Ctrlr/Controller/Helper/DepInjector.php"; // warum?
            $dependencyInjector = new Ctrlr_Controller_Helper_DepInjector();
			Zend_Controller_Action_HelperBroker::addHelper($dependencyInjector);
    }
}

?>