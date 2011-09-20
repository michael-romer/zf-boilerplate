<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Michael
 * Date: 15.09.11
 * Time: 06:44
 * To change this template use File | Settings | File Templates.
 */
namespace App\Webservice;

class Calls
{
    /**
     * Returns the input string reversed
     * @param App_Webservice_Types_Request_ReverseStringRequest $request
     * @return  App_Webservice_Types_Response_ReverseStringResponse $response
     */
    public function reverseString($request)
    {
        $sc = \Zend_Registry::get('sc');
        $srv = $sc->getService('stringReverser');
        $response = new \App_Webservice_Types_Response_ReverseStringResponse();
        $response->output = $srv->reverse($request->input);
        return $response;
    }

}
