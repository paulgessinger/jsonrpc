<?php


namespace PG\JsonRpc\tests;


use PG\JsonRpc\Exception\ArgumentException;
use PG\JsonRpc\Exception\ParseError;

class ArgumentExceptionTest extends ExceptionBaseTest {

    protected $msg = 'Invalid params' ;
    protected $code = -32602 ;

    protected function factory($id, $data) {
        $e = new ArgumentException($data, $id) ;

        return $e ;
    }

}
 