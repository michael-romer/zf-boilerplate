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
);
