<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Michael
 * Date: 16.09.11
 * Time: 03:59
 * To change this template use File | Settings | File Templates.
 */

namespace Boilerplate\Webservice\Soap;

class Server extends \Zend_Soap_Server {

    private $allowedKeys = array('123456');

    public function __construct($wsdl = null, array $options = null) {

        parent::__construct($wsdl,$options);

    }

        /**
     * Handle a request
     * overload the default ZF method
     *
     * @param string $request Optional request
     * @return void|string
     */
    public function handle($request = null) {

        if (null === $request) {
            $request = file_get_contents('php://input');
        }
        if (strlen($request) == 0) {
            $soap = $this->_getSoap();
            $soap->fault(401 , 'Message contains no XML');
        }

        $dom = new \DOMDocument();
        if(!$dom->loadXML($request)) {
            $soap = $this->_getSoap();
            $soap->fault(401 , 'Message contains invalid XML');
        } else {
            //strip out api_key stuff
            $xml = simplexml_load_string($request);
            $url = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];

            $children  = (array) $xml->children('http://schemas.xmlsoap.org/soap/envelope/')
                                     ->Body
                                     ->children($url);

            $methods = array_keys($children);
            $method =  $methods[0];

            $soap = $this->_getSoap();

            $apikey = (string) $xml->children('http://schemas.xmlsoap.org/soap/envelope/')
                                    ->Body
                                    ->children($url)
                                    ->{$method}
                                    ->children()
                                    ->apikey;

            if ($this->_hasAccess($apikey, $method)) {
                //strip api info
                unset($xml->children('http://schemas.xmlsoap.org/soap/envelope/')
                                    ->Body
                                    ->children($url)
                                    ->{$method}
                                    ->children()
                                    ->apikey);

                $request = $xml->asXml();

                //remove whitespace & empty lines
                $request = trim($request);
                $request = preg_replace("/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "\n", $request);
                return parent::handle($request);
                                } else {
                    $soap = $this->_getSoap();
                    $soap->fault(403 , 'Access forbidden');
                }
        }
    }

                /**
     * check if apikey has access to method
     *
     * @param string $apikey
     * @param string $method
     * @return bool
     */
    protected function _hasAccess($apikey, $method) {
        return in_array($apikey, $this->allowedKeys);
    }

}


