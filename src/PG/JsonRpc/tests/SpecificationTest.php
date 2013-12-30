<?php


namespace PG\JsonRpc\tests;


use PG\Common\JSON;
use PG\JsonRpc\Exception\ParseError;
use PG\JsonRpc\Request;
use PG\JsonRpc\Server;

/**
 * This class repeats a few texts with examples directly from the spec.
 * @package PG\JsonRpc\tests
 */
class SpecificationTest extends \PHPUnit_Framework_TestCase {

    private function factory($body ){

        $request = Request::create('', 'POST', array(), array(), array(), array(), $body) ;
        $server = new Server() ;
        return $server->handle($request) ;
    }

    private function assertJSONEquals($expected, $actual) {
        $this->assertEquals(
            JSON::decode($expected),
            JSON::decode($actual)
        ) ;
    }

    public function testNonExistendMethod() {
        $response = $this->factory('{"jsonrpc": "2.0", "method": "foobar", "id": "1"}') ;

        $this->assertJSONEquals(
            '{"jsonrpc": "2.0", "error": {"code": -32601, "message": "Method not found"}, "id": "1"}',
            $response->getContent()
        );
    }

    public function testInvalidJson() {
        try {
            $this->factory('{"jsonrpc": "2.0", "method": "foobar, "params": "bar", "baz]') ;
            $this->fail('Expected ParseError exception') ;
        }
        catch(ParseError $e) {
            $this->assertJSONEquals(
                '{"jsonrpc": "2.0", "error": {"code": -32700, "message": "Parse error"}, "id": null}',
                $e->toJSON()
            ) ;
        }

    }

    public function testInvalidRequestObject() {

        $this->assertJSONEquals(
            '{"jsonrpc": "2.0", "error": {"code": -32600, "message": "Invalid Request"}, "id": null}',
            $this->factory('{"jsonrpc": "2.0", "method": 1, "params": "bar"}')->getContent()
        );

    }

    public function testBatchInvalidJson() {
        $json = <<<JSON
[
  {"jsonrpc": "2.0", "method": "sum", "params": [1,2,4], "id": "1"},
  {"jsonrpc": "2.0", "method"
]
JSON;

        try {
            $this->factory($json) ;
            $this->fail('Expected ParseError exception') ;
        }
        catch(ParseError $e) {
            $this->assertJSONEquals(
                '{"jsonrpc": "2.0", "error": {"code": -32700, "message": "Parse error"}, "id": null}',
                $e->toJSON()
            ) ;
        }
    }

    public function testRpcEmptyArray() {
        $this->assertJSONEquals(
            '{"jsonrpc": "2.0", "error": {"code": -32600, "message": "Invalid Request"}, "id": null}',
            $this->factory('[]')->getContent()
        );
    }

    public function testRpcInvalidBatch() {
        $expected = <<<JSON
[
  {"jsonrpc": "2.0", "error": {"code": -32600, "message": "Invalid Request"}, "id": null}
]
JSON;


        $this->assertJSONEquals(
            $expected,
            $this->factory('[1]')->getContent()
        );
    }

    public function testMultipleInvalidBatchElements() {
        $expected = <<<JSON
[
  {"jsonrpc": "2.0", "error": {"code": -32600, "message": "Invalid Request"}, "id": null},
  {"jsonrpc": "2.0", "error": {"code": -32600, "message": "Invalid Request"}, "id": null},
  {"jsonrpc": "2.0", "error": {"code": -32600, "message": "Invalid Request"}, "id": null}
]
JSON;


        $this->assertJSONEquals(
            $expected,
            $this->factory('[1,2,3]')->getContent()
        );
    }


}
 