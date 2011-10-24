<?php

namespace ZendSkeletonModule\Controller;

use Zend\Mvc\Controller\ActionController;
use Zend\Mvc\InjectApplicationEvent;
use Zend\Mvc\LocatorAware;

class SkeletonController extends ActionController implements InjectApplicationEvent, LocatorAware
{
    public function indexAction()
    {
        return array('test' => "12345");
    }
}
