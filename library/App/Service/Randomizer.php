<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Michael
 * Date: 14.03.11
 * Time: 13:57
 * To change this template use File | Settings | File Templates.
 */
namespace App\Service;

class Randomizer
{
    public function getNumber($low, $high)
    {
        return rand($low, $high);
    }
}
