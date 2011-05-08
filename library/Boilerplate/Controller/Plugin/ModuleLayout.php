<?php

class Boilerplate_Controller_Plugin_ModuleLayout
    extends Zend_Controller_Plugin_Abstract
{
    public function routeShutdown(Zend_Controller_Request_Abstract $request)
    {
        Zend_Layout::getMvcInstance()->setLayout($request->getModuleName());

        Zend_Layout::getMvcInstance()->setLayoutPath(
            APPLICATION_PATH . "/modules/" . $request->getModuleName() .
            "/layouts/scripts"
        );

        $eh = Zend_Controller_Front::getInstance()->getPlugin(
            "Zend_Controller_Plugin_ErrorHandler"
        );

        $eh->setErrorHandlerModule($request->getModuleName());
    }
}
