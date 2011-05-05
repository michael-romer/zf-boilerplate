<?php

require_once 'PHPUnit/Framework/TestCase.php';

class IndexControllerTest extends Zend_Test_PHPUnit_ControllerTestCase
{

    public function setUp()
    {
        /* Setup Routine */
    }

    public function tearDown()
    {
        /* Tear Down Routine */
    }

    public function testRoutes()
    {
        $uri = "/";
        $request = $this->getFrontController()->getRouter()->route($this->getRequest()->setRequestUri($uri));
        $this->assertAction('index');
        $this->assertController('index');
        $this->assertModule('site');

        $this->resetRequest();

        $uri = "/homepage";
        $request = $this->getFrontController()->getRouter()->route($this->getRequest()->setRequestUri($uri));
        $this->assertAction('index');
        $this->assertController('index');
        $this->assertModule('site');

    }

}

