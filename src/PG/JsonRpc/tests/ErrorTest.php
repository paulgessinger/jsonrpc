<?php


namespace PG\JsonRpc\tests;

use PG\Common\JSON;
use PG\JsonRpc\Exception\InternalError;
use PG\JsonRpc\Request;
use PG\JsonRpc\Server;

class ErrorTest extends \PHPUnit_Framework_TestCase {
    private $server ;


    public function setUp() {
        $this->server = new Server() ;
        $this->server->expose('Sample', '\PG\JsonRpc\tests\sample\Sample') ;
    }

    /**
     * This one is a bit tricky. Problem is, if the error handler does
     * not exist, we get two json responses, since the shutdown function
     * apparently gets these errors, and prints a response body.
     * This leads to invalid json, which clients could not parse => bad!
     *
     * This error handler discards all previous output,
     */
    public function testErrorHandler() {



        // test the error handler directly
        try {
            trigger_error('Sample', E_USER_WARNING) ;
        }
        catch(\Exception $e) {
            $this->fail('We dont expect an exception from warnings and notices, they are to be discarded.') ;
        }

        try {
            trigger_error('Sample', E_USER_NOTICE) ;
        }
        catch(\Exception $e) {
            $this->fail('We dont expect an exception from warnings and notices, they are to be discarded.') ;
        }

        try {
            trigger_error('Sample', E_USER_ERROR) ;
            $this->fail('Errors are supposed to lead to an internal exception.') ;
        }
        catch(InternalError $e) {}


        /**
         * test error handling inside responses
         * this makes sure the server generates valid JSON,
         * no matter what
         */
        $response = $this->call('Sample.triggerError') ;
        $this->assertArrayHasKey('error', $response) ;
        $this->assertArrayNotHasKey('result', $response) ;
        $this->assertEquals(-32603, $response['error']['code']) ;
        $this->assertEquals('Internal error', $response['error']['message']) ;

        $response = $this->call('Sample.triggerWarning') ;
        $this->assertArrayHasKey('result', $response) ;
        $this->assertArrayNotHasKey('error', $response) ;
        $this->assertEquals('successful result', $response['result']) ;

        $response = $this->call('Sample.triggerNotice') ;
        $this->assertArrayHasKey('result', $response) ;
        $this->assertArrayNotHasKey('error', $response) ;
        $this->assertEquals('successful result', $response['result']) ;
    }

    private function call($method) {
        return JSON::decode($this->server->handle(Request::createRPC($method))->getContent()) ;
    }

}
 