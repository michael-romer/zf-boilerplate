<?php

class Site_IndexController extends Zend_Controller_Action
{

    /**
     * @var Doctrine\ORM\EntityManager
     * 
     * 
     */
    protected $_em = null;

    /**
     * @var \sfServiceContainer
     * 
     * 
     */
    protected $_sc = null;

    /**
     * @var \App\Service\RandomStringGenerator
     * @InjectService RandomStringGenerator
     * 
     * 
     */
    protected $_randomStringGenerator = null;

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {   
        $this->_em = Zend_Registry::get('em');
        $newPost = new \App\Entity\Post();
        $newPost->setTitle($this->_randomStringGenerator->createString());

        try
        {
            $this->_em->persist($newPost);
            $this->_em->flush();
        }
        catch (Exception $e)
        {
            $this->view->databaseError = true;
        }

        try
        {
            $data = $this->_em->getRepository("\App\Entity\Post")->findAll();
            $this->view->data = $data;
        }
        catch (Exception $e)
        {
            $this->view->databaseError = true;
        }
    }

    public function headerAction()
    {
        $container = new Zend_Navigation(
            array(
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
                    'uri'        => 'https://github.com/michael-romer/' .
                                        'zf-boilerplate',
                    'label'      => 'GitHub'
                )
            )
        );

        $this->view->navigation($container);
    }

    public function footerAction()
    {
        
    }


}