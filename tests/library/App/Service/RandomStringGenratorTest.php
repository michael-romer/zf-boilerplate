<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Michael
 * Date: 14.03.11
 * Time: 13:57
 * To change this template use File | Settings | File Templates.
 */
namespace App\Service;

class RandomStringGeneratorTest extends \PHPUnit_Framework_TestCase {

    public function testCreateString()
    {
        $stub = $this->getMock('App\Service\StringReverser');
        $stub->expects($this->any())
             ->method('reverse')
             ->will($this->returnArgument(0));

        $service = new RandomStringGenerator($stub);
        $this->assertContainsOnly('string', array($service->createString()));
    }

}
