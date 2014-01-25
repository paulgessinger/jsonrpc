<?php


namespace PG\JsonRpc\tests;


use Monolog\Handler\NullHandler;
use Monolog\Logger;
use PG\Common\JSON;
use PG\JsonRpc\BatchResult;
use PG\JsonRpc\Result;

class BatchResultTest extends \PHPUnit_Framework_TestCase {

    private $app ;

    public function setUp() {
        $this->app = new \Pimple() ;
        $this->app['logger'] = new Logger('phpunit', array(new NullHandler())) ;
    }

    public function tearDown() {
        unset($this->logger) ;
    }

    public function testConstruct() {
        new BatchResult($this->app, array()) ;
    }

    public function testToArrayInvalidResults() {
        $batch = new BatchResult($this->app, array(
            'something_wrong'
        )) ;

        $this->setExpectedException('\PG\JsonRpc\Exception\InternalError') ;

        $batch->toArray() ;
    }

    /**
     * @covers \PG\JsonRpc\BatchResult::toArray
     */
    public function testToArray() {
        $batch = new BatchResult($this->app, array(
            new Result('id', 'some_result')
        )) ;

        $array = $batch->toArray() ;

        $this->assertEquals(array(
            array(
                'id' => 'id',
                'jsonrpc' => '2.0',
                'result' => 'some_result'
            )
        ), $array) ;
    }

    /**
     * @covers \PG\JsonRpc\BatchResult::toArray
     * @covers \PG\JsonRpc\AbstractResult::toJSON
     */
    public function testToJson() {
        $batch = new BatchResult($this->app, array(
            new Result('id', 'some_result')
        )) ;

        $json = $batch->toJSON() ;

        $this->assertEquals(JSON::encode(array(
            array(
                'jsonrpc' => '2.0',
                'result' => 'some_result',
                'id' => 'id'
            )
        )), $json) ;
    }

}
 