<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Michael
 * Date: 14.03.11
 * Time: 13:57
 * To change this template use File | Settings | File Templates.
 */
namespace App\Service;

class RandomizerTest extends \PHPUnit_Framework_TestCase {

    public function testGetNumber()
    {
        $service = new Randomizer();

        for($i = 0; $i <= 100; $i++)
        {
            $number = $service->getNumber(1,10);
            $this->assertRegExp('/[1-10]?/', (string) $number);
        }

    }

}
