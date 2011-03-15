<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Michael
 * Date: 14.03.11
 * Time: 13:57
 * To change this template use File | Settings | File Templates.
 */
namespace Ctrlr\Service;

class Testservice {

    protected $string;
    protected $reverser;

    public function __construct($string, $reverser)
    {
        $this->string = $string;
        $this->reverser = $reverser;
    }

    public function test()
    {
        return $this->reverser->reverse($this->string);
    }
}
