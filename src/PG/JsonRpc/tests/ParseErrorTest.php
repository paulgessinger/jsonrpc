<?php


namespace PG\JsonRpc\tests;


use PG\JsonRpc\Exception\ParseError;

class ParseErrorTest extends ExceptionBaseTest {

    protected $msg = 'Parse error' ;
    protected $code = -32700 ;

    protected function factory($id, $data) {
        $e = new ParseError($data) ;
        $e->setId($id) ;

        return $e ;
    }

}
 