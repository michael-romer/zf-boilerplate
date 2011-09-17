<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Michael
 * Date: 15.09.11
 * Time: 06:44
 * To change this template use File | Settings | File Templates.
 */
namespace App\Webservice;

class Calls {
    /**
     * Returns the input string reversed
     * @param string $input
     * @return  string
     */
    public function reverseString($input = "") {
        $sc = \Zend_Registry::get('sc');
        $srv = $sc->getService('stringReverser');
        return $srv->reverse($input);
    }

}
