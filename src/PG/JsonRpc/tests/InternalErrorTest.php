<?php


namespace PG\JsonRpc\tests;


use PG\JsonRpc\Exception\InternalError;
use PG\JsonRpc\Exception\ParseError;

class InternalErrorTest extends ExceptionBaseTest {

    protected $msg = 'Internal error' ;
    protected $code = -32603 ;

    protected function factory($id, $data) {
        return new InternalError($this->msg, $this->code, $data, $id) ;
    }

}
 