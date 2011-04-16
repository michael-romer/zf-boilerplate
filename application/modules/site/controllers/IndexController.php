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

        try
        {
            $this->em->persist($randy);
            $this->em->flush();
        }
        catch (Exception $e)
        {
            $this->view->databaseError = true;
        }

        try
        {
            $this->view->data = $this->em->getRepository("\Ctrlr\Entity\Person")->findAll();
        }
        catch (Exception $e)
        {
            $this->view->databaseError = true;
        }

       // echo $this->testservice->test();
    }

    public function headerAction()
    {
        $container = new Zend_Navigation(array(
            array(
                'action'     => 'index',
                'controller' => 'index',
                'module'     => 'site',
                'label'      => 'welcome'
            ),
            array(
                'uri'        => 'http://www.zf-boilerplate.com/',
                'label'      => 'project website'
            ),
            array(
                'uri'        => 'https://github.com/michael-romer/zf-boilerplate',
                'label'      => 'GitHub'
            )
        ));

        $this->view->navigation($container);
    }

    public function footerAction()
    {
        // action body
    }


}