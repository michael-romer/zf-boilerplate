<?php

class Boilerplate_Controller_Helper_DependencyInjector
    extends Zend_Controller_Action_Helper_Abstract
{
    /**
     * Takes care of injecting controller dependencies
     * into the controller at runtime.
     */
    public function preDispatch()
    {
        $actionController = $this->getActionController();

        $r = new Zend_Reflection_Class($actionController);
        $properties = $r->getProperties();

        foreach ($properties as $property) {
            if ($property->getDeclaringClass()->getName() ==
               get_class($actionController)) {

                if ($property->getDocComment() &&
                   $property->getDocComment()->hasTag('InjectService')) {
                    
                    $tag = $property->getDocComment()->getTag('InjectService');

                    if ($tag->getDescription()) {

                        $sc = Zend_Registry::get('sc');

                        $service = $sc->getService(
                            trim(lcfirst($tag->getDescription()))
                        );

                        $property->setAccessible(true);
                        $property->setValue($actionController, $service);
                    }
                    else
                        throw new Exception("No service key given");
                }
            }
        }
    }
}
