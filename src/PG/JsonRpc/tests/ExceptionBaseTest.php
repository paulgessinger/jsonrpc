<?php


namespace PG\JsonRpc\tests;


use PG\Common\JSON;
use PG\JsonRpc\Server;

abstract class ExceptionBaseTest extends \PHPUnit_Framework_TestCase {

    protected $code ;
    protected $msg ;
    private $debug ;

    /**
     * @param $id
     * @param $data
     * @return \PG\JsonRpc\Exception\AbstractException
     */
    abstract protected function factory($id, $data) ;

    public function setUp() {
        $this->debug = Server::$debug ;
        Server::$debug = true ;
    }

    public function tearDown() {
        Server::$debug = $this->debug ;
    }

    public function testConstruct() {
        $this->factory('phpunit', 'phpunit data') ;
    }

    public function testFormatValid() {
        $e = $this->factory('phpunit', 'phpunit data') ;

        $output = JSON::decode($e->toJSON()) ;

        $this->assertEquals(array(
            'id' => 'phpunit',
            'jsonrpc' => '2.0',
            'error' => array(
                'code' => $this->code,
                'message' => $this->msg,
                'data' => 'phpunit data'
            )
        ), $output) ;
    }

} 