<?php
return array(
    'di' => array(
        'instance' => array(
            'alias' => array(
                'skeleton' => 'ZendSkeletonModule\Controller\SkeletonController',
            ),
            'Zend\View\PhpRenderer' => array(
                'parameters' => array(
                    'options'  => array(
                        'script_paths' => array(
                            'skeleton' => __DIR__ . '/../views',
                        ),
                    ),
                ),
            ),
        ),
    ),
    'routes' => array(
        'home' => array(
            'type' => 'Zend\Mvc\Router\Http\Literal',
            'options' => array(
                'route'    => '/huhus',
                'defaults' => array(
                    'controller' => 'skeleton',
                    'action'     => 'index',
                ),
            ),
        ),
    ),
);