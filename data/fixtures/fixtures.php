<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Michael
 * Date: 15.03.11
 * Time: 18:32
 * To change this template use File | Settings | File Templates.
 */
$newPost = new \App\Entity\Post();
$newPost->setTitle("Post added by DB fixture");
$em->persist($newPost);
$em->flush();

