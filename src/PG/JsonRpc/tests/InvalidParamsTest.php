<?php


namespace PG\JsonRpc\tests;


use PG\JsonRpc\Exception\InternalError;
use PG\JsonRpc\Exception\InvalidParams;
use PG\JsonRpc\Exception\ParseError;

class InvalidParamsTest extends ExceptionBaseTest {

    protected $msg = 'Invalid params' ;
    protected $code = -32602 ;

    protected function factory($id, $data) {
        $e = new InvalidParams($data) ;
        $e->setId($id) ;
        return $e ;
    }

}
 