<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Michael
 * Date: 18.09.11
 * Time: 03:29
 * To change this template use File | Settings | File Templates.
 */

namespace App\Webservice;

class CallsTest extends \PHPUnit_Framework_TestCase {

    public function testReverseString()
    {
        $calls = new Calls();
        $request = new \App_Webservice_Types_Request_ReverseStringRequest();
        $request->input = "test";
        $response = $calls->reverseString($request);

        var_dump($response);
    }
}
