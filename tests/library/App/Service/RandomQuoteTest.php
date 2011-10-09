<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Michael
 * Date: 14.03.11
 * Time: 13:57
 * To change this template use File | Settings | File Templates.
 */
namespace App\Service;

class RandomQuoteTest extends \PHPUnit_Framework_TestCase {

    public function testGetQuote()
    {
        $stub = $this->getMock('App\Service\Randomizer');
        $stub->expects($this->any())
             ->method('getNumber')
             ->will($this->returnValue('0'));

        $service = new RandomQuote($stub);
        $quote = $service->getQuote();
        $this->assertRegExp('/There are only two ways to live your life./', $quote[0]);
    }

}
