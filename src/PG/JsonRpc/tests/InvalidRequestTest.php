<?php


namespace PG\JsonRpc\tests;


use PG\Common\JSON;
use PG\JsonRpc\Exception\InvalidRequest;

class InvalidRequestTest extends \PHPUnit_Framework_TestCase {

    public function testToArray() {
        $exception = new InvalidRequest() ;
        $exception->setId('id') ;

        $array = $exception->toArray() ;

        $this->assertEquals(array(
            'id' => 'id',
            'jsonrpc' => '2.0',
            'error' => array(
                'code' => -32600,
                'message' => 'Invalid Request'/*,
                'data' => ''*/
            )
        ), $array) ;
    }

    public function testToJson() {
        $exception = new InvalidRequest() ;
        $exception->setId('id') ;

        $array = JSON::decode($exception->toJSON()) ;

        $this->assertEquals(array(
            'id' => 'id',
            'jsonrpc' => '2.0',
            'error' => array(
                'code' => -32600,
                'message' => 'Invalid Request'/*,
                'data' => ''*/
            )
        ), $array) ;
    }

}
 