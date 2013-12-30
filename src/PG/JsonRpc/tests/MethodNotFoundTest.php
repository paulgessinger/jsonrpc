<?php


namespace PG\JsonRpc\tests;


use PG\JsonRpc\Exception\InternalError;
use PG\JsonRpc\Exception\InvalidParams;
use PG\JsonRpc\Exception\MethodNotFound;
use PG\JsonRpc\Exception\ParseError;

class MethodNotFoundTest extends ExceptionBaseTest {

    protected $msg = 'Method not found' ;
    protected $code = -32601 ;

    protected function factory($id, $data) {
        $e = new MethodNotFound($data) ;
        $e->setId($id) ;
        return $e ;
    }

}
 