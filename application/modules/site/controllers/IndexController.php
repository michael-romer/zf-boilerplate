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
     * @var \App\Service\RandomStringGenerator
     * @InjectService
     * 
     * 
     */
    protected $randomStringGenerator = null;

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {   
        $this->em = Zend_Registry::get('doctrine')->getEntityManager();
        $newPost = new \App\Entity\Post();
        $newPost->setTitle($this->randomStringGenerator->createString());

        try
        {
            $this->em->persist($newPost);
            $this->em->flush();
        }
        catch (Exception $e)
        {
            $this->view->databaseError = true;
        }

        try
        {
            $this->view->data = $this->em->getRepository("\App\Entity\Post")->findAll();
        }
        catch (Exception $e)
        {
            $this->view->databaseError = true;
        }
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

    public function footerAction() {}


}