<?php


namespace PG\JsonRpc\tests;


use PG\JsonRpc\Result;

class ResultTest extends \PHPUnit_Framework_TestCase {

    public function testToArray() {
        $result = new Result('id', 'some_result') ;

        $array = $result->toArray() ;

        $this->assertEquals(array(
            'id' => 'id',
            'jsonrpc' => '2.0',
            'result' => 'some_result'
        ), $array) ;
    }

}
 