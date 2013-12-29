<?php
namespace PG\JsonRpc ;

use PG\JsonRpc\Exception\InvalidRequest;
use PG\JsonRpc\Exception\ParseError;
use Symfony\Component\HttpFoundation\Request as HttpRequest ;
use PG\Common\JSON;

class Request extends HttpRequest  {

    public static function createRPC($method, $params = array()) {

        /**
         * Constructor.
         *
         * @param array  $query      The GET parameters
         * @param array  $request    The POST parameters
         * @param array  $attributes The request attributes (parameters parsed from the PATH_INFO, ...)
         * @param array  $cookies    The COOKIE parameters
         * @param array  $files      The FILES parameters
         * @param array  $server     The SERVER parameters
         * @param string $content    The raw body data
         *
         * @api
         */

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