<?php


namespace PG\JsonRpc\tests;


use PG\Common\JSON;
use PG\JsonRpc\Response;
use PG\JsonRpc\Result;

class ResponseTest extends \PHPUnit_Framework_TestCase {

    public function testConstruct() {
        $response = new Response() ;

        $this->assertTrue($response->headers->has('Content-Type')) ;
        $this->assertEquals('application/json', $response->headers->get('Content-Type')) ;
    }

    /**
     * @covers \PG\JsonRpc\Response::setResult
     */
    public function testSetResult() {
        $response = new Response() ;
        $result = new Result(1, 'result') ;


        $response->setResult($result) ;

        $content = JSON::decode($response->getContent()) ;
        $this->assertEquals('result', $content['result']) ;
    }

}
 