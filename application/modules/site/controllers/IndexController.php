<?php

class Site_IndexController extends Zend_Controller_Action
{

    /**
     * @var Doctrine\ORM\EntityManager
     * 
     * 
     */
    protected $em = null;

    /**
     * @var \sfServiceContainer
     * 
     * 
     */
    protected $sc = null;

    /**
     * @var \Ctrlr\Service\Testservice
     * @InjectService
     * 
     * 
     */
    protected $testservice = null;

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        $this->em = Zend_Registry::get('doctrine')->getEntityManager();
        $randy = new \Ctrlr\Entity\Person();
        $randy->setGewicht(200);
        $randy->setVorname("Michael");
        $randy->setNachname("Romer");
        $this->em->persist($randy);
        $this->em->flush();

        $persons = $this->em->getRepository("\Ctrlr\Entity\Person")->findAll();
        echo $this->testservice->test();
    }

    public function headerAction()
    {
        $page = new Zend_Navigation_Page_Mvc(array(
            'action'     => 'index',
            'controller' => 'index',
            'module'     => 'my'
        ));
    }

    public function footerAction()
    {
        // action body
    }


}





