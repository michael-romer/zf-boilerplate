<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Michael
 * Date: 18.09.11
 * Time: 03:29
 * To change this template use File | Settings | File Templates.
 */

namespace App\Webservice;

require_once 'PHPUnit/Framework/TestCase.php';

class CallsTest extends \PHPUnit_Framework_TestCase {

    public function testRandomQuote()
    {
        $calls = new Calls();
        $request = new \App_Webservice_Types_Request_RandomQuoteRequest();
        $response = $calls->randomQuote($request);
        $this->assertType('App_Webservice_Types_Response_RandomQuoteResponse', $response);
        $this->assertType('string', $response->quote->wording);
        $this->assertType('string', $response->quote->author);
    }

    public function testQuote()
    {
        $calls = new Calls();
        $request = new \App_Webservice_Types_Request_QuoteRequest();
        $request->id = 1;
        $response = $calls->quote($request);
        $this->assertType('App_Webservice_Types_Response_QuoteResponse', $response);
        $this->assertType('string', $response->quote->wording);
        $this->assertType('string', $response->quote->author);
    }

}
