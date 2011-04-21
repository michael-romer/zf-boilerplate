<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Michael
 * Date: 14.03.11
 * Time: 13:57
 * To change this template use File | Settings | File Templates.
 */
namespace App\Service;

class StringReverserTest extends \PHPUnit_Framework_TestCase {

    public function testReverse()
    {
       // require_once "../../../../library/App/Service/StringReverser.php";
        $service = new StringReverser();
        $this->assertEquals('12345', $service->reverse('54321'));
    }

}
