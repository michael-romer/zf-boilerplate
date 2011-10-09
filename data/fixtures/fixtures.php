<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Michael
 * Date: 15.03.11
 * Time: 18:32
 * To change this template use File | Settings | File Templates.
 */
$newQuote = new \App\Entity\Quote();
$newQuote->setWording("Don’t let the past steal your present.");
$newQuote->setAuthor("Cherralea Morgen");
$em->persist($newQuote);
$em->flush();