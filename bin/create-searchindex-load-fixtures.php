<?php
require_once('bootstrap.php');
require_once('create-searchindex.php');

try {
    $client = Zend_Registry::get('es');
    $index = $client->getIndex(INDEX_LABEL);
    $type = $index->getType('quote');

    $em = Zend_Registry::get('em');
    $quotes = $em->getRepository("\App\Entity\Quote")->findAll();

    foreach($quotes as $quote)
    {
        $doc = new Elastica_Document(1,
           array('id' => $quote->getId(), 'wording' => $quote->getWording(), 'author' => $quote->getAuthor())
        );
        
        $type->addDocument($doc);
    }

    $index->refresh();
    echo "Fixtures successfully loaded." . PHP_EOL;
} catch (Exception $e) {
    echo "Fixtures could not be loaded." . PHP_EOL;
}