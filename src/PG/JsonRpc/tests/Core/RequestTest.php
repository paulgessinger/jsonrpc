<?php


namespace PG\JsonRpc\tests\Core;


use PG\JsonRpc\Request;

class RequestTest extends \PHPUnit_Framework_TestCase {

    public function testExtractSingle() {
        $request = Request::create('', 'POST', array(), array(), array(), array(), '{"jsonrpc":"2.0","id":1,"method":"Sample.divide","params": [11, 5]}') ;

        $extracted = $request->extract() ;

        $this->assertEquals(array(array(
            'jsonrpc' => '2.0',
            'id' => 1,
            'method' => 'Sample.divide',
            'params' => array(11, 5)
        )), $extracted) ;
    }

    public function testExtractMultiple() {
        $request = Request::create('', 'POST', array(), array(), array(), array(),
            '[{"jsonrpc":"2.0","id":1,"method":"Sample.divide","params": [11, 5]}, {"jsonrpc":"2.0","id":2,"method":"Sample.divide","params": [17, 3]}]') ;

        $extracted = $request->extract() ;

        $this->assertEquals(array(
            array(
                'jsonrpc' => '2.0',
                'id' => 1,
                'method' => 'Sample.divide',
                'params' => array(11, 5)
            ),
            array(
                'jsonrpc' => '2.0',
                'id' => 2,
                'method' => 'Sample.divide',
                'params' => array(17, 3)
            )
        ), $extracted) ;
    }

    public function testExtractFailure() {
        $request = Request::create('', 'POST', array(), array(), array(), array(), '{"jsonrpc":"2.0","id":1,"method":"Sample.divide","params": [],}') ;

        $this->setExpectedException('PG\JsonRpc\Exception\ParseError') ;

        $request->extract() ;
    }

}
 