<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Michael
 * Date: 14.03.11
 * Time: 13:57
 * To change this template use File | Settings | File Templates.
 */
namespace App\Service;

class RandomStringGenerator
{
    protected $_reverser;

    public function __construct($_reverser)
    {
        $this->reverser = $_reverser;
    }

    public function createString()
    {
        $n = rand(10e16, 10e20);
        $createdString = base_convert($n, 10, 36);
        return $this->reverser->reverse($createdString);
    }
}
