<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Michael
 * Date: 15.03.11
 * Time: 18:32
 * To change this template use File | Settings | File Templates.
 */
$randy = new \Ctrlr\Entity\Person();
$randy->setGewicht(200);
$randy->setVorname("Michael");
$randy->setNachname("Romer");
$em->persist($randy);
$em->flush();

