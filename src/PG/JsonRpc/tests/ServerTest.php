<?php


namespace PG\JsonRpc\tests ;


use Monolog\Logger;
use PG\Common\JSON;
use PG\JsonRpc\Exception\MethodNotFound;
use PG\JsonRpc\Exception\ParseError;
use PG\JsonRpc\Request;
use PG\JsonRpc\Server;

class ServerTest extends \PHPUnit_Framework_TestCase {

    /**
     * @var \PG\JsonRpc\Server
     */
    private $server ;

    public function setUp() {
        $this->server = new Server(true) ;
    }

    public function tearDown() {
        unset($this->server) ;
        \Mockery::close() ;
    }

    /**
     * @covers \PG\JsonRpc\Server::__construct
     */
    public function testConstruct() {
        var_dump($this->server['logger']) ;
        $this->assertInstanceOf('Monolog\Logger', $this->server['logger']) ;
    }

    public function testConstructExplicitLogger() {
        $logger = new Logger('PHPUnit') ;

        $server = new Server(false) ;
        $server['logger'] = $logger ;

        $this->assertInstanceOf('Monolog\Logger', $server['logger']) ;
        $this->assertEquals($logger, $server['logger']) ;
    }

    /**
     * @covers \PG\JsonRpc\Server::run
     */
    public function testRun() {
        $this->setExpectedException('PG\JsonRpc\Exception\InvalidRequest') ;

        $this->server = new Server() ;
        $this->server->run() ;
    }

    /**
     * @covers \PG\JsonRpc\Server::handle
     */
    public function testInvalidJson() {
        $request = Request::create('', 'POST', array(), array(), array(), array(), '{"jsonrpc":"2.0","id":1,"method":"Sample.divide","params": [],}') ;

        $this->setExpectedException('PG\JsonRpc\Exception\ParseError') ;

        $this->server->handle($request) ;
    }

    /**
     * @covers \PG\JsonRpc\Server::expose
     */
    public function testExposeClassNotExist() {
        $this->setExpectedException('PG\JsonRpc\Exception\ClassNotExists') ;

        $this->server->expose('Sample', 'Does\Not\Exist') ;
    }

    /**
     * @covers \PG\JsonRpc\Server::expose
     */
    public function testExposeClassExist() {

        \Mockery::mock('\Does\Exist') ;

        $this->server->expose('Sample', 'Does\Exist') ;
    }

    /**
     * @covers \PG\JsonRpc\Server::handle
     */
    public function testRouting() {
        $this->server->expose('Sample', 'PG\JsonRpc\tests\sample\Sample') ;
        $request = Request::createRPC('Sample.divide', array(17, 4)) ;

        $response = $this->server->handle($request) ;
        $result = JSON::decode($response->getContent()) ;

        $this->assertEquals(17/4, $result['result']) ;
    }

    /**
     * @covers \PG\JsonRpc\Server::handle
     */
    public function testInvalidParameters() {
        $this->server->expose('Sample', 'PG\JsonRpc\tests\sample\Sample') ;

        $request = Request::create('', 'POST', array(), array(), array(), array(),
            '{"jsonrpc":"2.0","id":1,"method":"Sample.divide","params": [10]}') ;

        $response = $this->server->handle($request) ;
        $result = JSON::decode($response->getContent()) ;

        $this->assertArrayHasKey('error', $result) ;

        $error = $result['error'] ;

        $this->assertArrayHasKey('code', $error) ;
        $this->assertArrayHasKey('message', $error) ;

        $this->assertEquals(-32602, $error['code']) ;
    }

    /**
     * @covers \PG\JsonRpc\Server::handle
     */
    public function testIdIsPreserved() {
        $this->server->expose('Sample', 'PG\JsonRpc\tests\sample\Sample') ;

        $request = Request::create('', 'POST', array(), array(), array(), array(),
            '[{"jsonrpc":"2.0","id":1,"method":"Sample.divide","params": [10, 5]},
             {"jsonrpc":"2.0","id":"fourteen","method":"Sample.divide","params": [11, 5]}]') ;

        $response = $this->server->handle($request) ;
        $result = JSON::decode($response->getContent()) ;

        $this->assertEquals(array(
            array(
                'jsonrpc' => '2.0',
                'id' => 1,
                'result' => 10/5
            ),
            array(
                'jsonrpc' => '2.0',
                'id' => 'fourteen',
                'result' => 11/5
            ),
        ), $result) ;

    }

    public function testHandleException() {
        ob_start() ;

        $this->server->handleException(new ParseError('something went wrong')) ;

        $ob = ob_get_clean() ;

        $output = JSON::decode($ob) ;

        $this->assertEquals(array(
            'id' => null,
            'jsonrpc' => '2.0',
            'error' => array(
                'code' => -32700,
                'data' => 'something went wrong',
                'message' => 'Parse error'
            )
        ), $output) ;
    }
}
 