<?php
namespace PG\JsonRpc ;

use PG\JsonRpc\Exception\InvalidRequest;
use PG\JsonRpc\Exception\ParseError;
use Symfony\Component\HttpFoundation\Request as HttpRequest ;
use PG\Common\JSON;

/**
 * Based on Symfony's HttpFoundation\Request. Only adds a few methods,
 * and touches nothing the Class itself does.
 *
 * @package PG\JsonRpc
 */
class Request extends HttpRequest  {

    private $batch = false ;

    public function isBatch() {
        return $this->batch ;
    }

    /**
     * Factory for creating a request object specific to
     * a json rpc call (eg. no POST, GET or other data.
     *
     * @param $method
     * @param array $params
     * @return Request
     */
    public static function createRPC($method, $params = array()) {

        $req = JSON::encode(array(
            'jsonrpc' => '2.0',
            'id' => null,
            'method' => $method,
            'params' => $params
        )) ;

        return new self(
            array(),
            array(),
            array(),
            array(),
            array(),
            array(),
            $req
        ) ;

    }

    /**
     * Extracts an array of calls from the JSON request body.
     * Normalizes that there can be one request object, or an
     * array of multiple request objects.
     *
     * @return array
     * @throws Exception\ParseError
     * @throws Exception\InvalidRequest
     */
    public function extract() {
        $body = $this->getContent() ;

        try {
            $data = JSON::decode($body) ;
        }
        catch(JSON\Exception\AbstractException $e) {
            throw new ParseError($e->getMessage()) ;
        }

        if(!is_array($data)) {
            throw new InvalidRequest ;
        }

        $calls = array() ;

        if(array_keys($data) === range(0, count($data) - 1)) {
            // this one is a batch request

            $this->batch = true ;

            foreach($data as $request_data) {
                $calls[] = $request_data ;
            }

        } else {
            // this one is a regular single request
            $calls[] = $data ;
        }

        return $calls ;
    }

}