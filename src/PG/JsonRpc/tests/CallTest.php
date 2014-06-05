<?php


namespace PG\JsonRpc\tests;


use Monolog\Handler\NullHandler;
use Monolog\Logger;
use PG\JsonRpc\Call;
use PG\JsonRpc\Exception\InvalidParams;
use PG\JsonRpc\Exception\InvalidRequest;
use PG\JsonRpc\Server;

class CallTest extends \PHPUnit_Framework_TestCase {

    /**
     * @var \Pimple
     */
    private $app ;

    public function __construct() {
        $this->app = new \Pimple() ;
        parent::__construct() ;
    }

    public function setUp() {
        $this->app['logger'] = new Logger('phpunit', array(new NullHandler())) ;
        Server::$debug = true ;
    }

    public function tearDown() {

        \Mockery::close() ;
    }

    private function makeCall($method, $params = array()) {
        return array(
            'id' => 'id',
            'jsonrpc' => '2.0',
            'method' => $method,
            'params' => $params
        ) ;
    }

    public function testConstructSuccess() {
        $call = new Call(
            $this->makeCall('Sample.divide'),
            array(
                'Sample' => 'PG\JsonRpc\tests\sample\Sample'
            ),
            $this->app
        );
    }

    public function testGetVersion() {
        $call = new Call(
            $this->makeCall('Sample.divide'),
            array(
                'Sample' => 'PG\JsonRpc\tests\sample\Sample'
            ),
            $this->app
        );

        $this->assertEquals('2.0', $call->getVersion()) ;
    }

    public function testConstructInvalidVersion() {
        // wrong version number
        try {
            new Call(
                array(
                    'id' => null,
                    'jsonrpc' => '2.1',
                    'method' => 'Sample.divide',
                    'params' => array()
                ),
                array(
                    'Sample' => 'PG\JsonRpc\tests\sample\Sample'
                ),
                $this->app
            );

            $this->fail('Exception of type PG\JsonRpc\Exception\InvalidRequest was expected') ;
        }
        catch(InvalidRequest $e) {}

        // version number missing
        try {
            new Call(
                array(
                    'id' => null,
                    'method' => 'Sample.divide',
                    'params' => array()
                ),
                array(
                    'Sample' => 'PG\JsonRpc\tests\sample\Sample'
                ),
                $this->app
            );

            $this->fail('Exception of type PG\JsonRpc\Exception\InvalidRequest was expected') ;
        }
        catch(InvalidRequest $e) {}

    }

    /*public function testConstructNoId() {
        try {
            new Call(
                array(
                    'jsonrpc' => '2.0',
                    'method' => 'Sample.divide',
                    'params' => array()
                ),
                array(
                    'Sample' => 'PG\JsonRpc\tests\sample\Sample'
                ),
                $this->app
            );

            $this->fail('Exception of type PG\JsonRpc\Exception\InvalidRequest was expected') ;
        }
        catch(InvalidRequest $e) {}
    }*/

    public function testConstructNotObject() {
        try {
            new Call(
                'wrong',
                array(
                    'Sample' => 'PG\JsonRpc\tests\sample\Sample'
                ),
                $this->app
            );

            $this->fail('Exception of type PG\JsonRpc\Exception\InvalidRequest was expected') ;
        }
        catch(InvalidRequest $e) {}
    }

    public function testInvalidParameters() {
        // params not array
        try {
            new Call(
                array(
                    'id' => null,
                    'jsonrpc' => '2.0',
                    'method' => 'Sample.divide',
                    'params' => ''
                ),
                array(
                    'Sample' => 'PG\JsonRpc\tests\sample\Sample'
                ),
                $this->app
            );

            $this->fail('Exception of type PG\JsonRpc\Exception\InvalidParams was expected') ;
        }
        catch(InvalidParams $e) {}
    }

    /**
     * @covers \PG\JsonRpc\Call::execute
     */
    public function testExecuteSuccess() {
        $call = new Call(
            $this->makeCall('Sample.divide', array(5, 10)),
            array(
                'Sample' => 'PG\JsonRpc\tests\sample\Sample'
            ),
            $this->app
        );

        $result = $call->execute() ;

        $this->assertInstanceOf('PG\JsonRpc\ResultInterface', $result) ;
        $this->assertNotInstanceOf('PG\JsonRpc\Exception\AbstractException', $result) ;
    }

    public function testExecuteInvalidParams() {
        $call = new Call(
            $this->makeCall('Sample.divide', array(5)),
            array(
                'Sample' => 'PG\JsonRpc\tests\sample\Sample'
            ),
            $this->app
        );

        $result = $call->execute() ;

        $this->assertInstanceOf('PG\JsonRpc\ResultInterface', $result) ;
        $this->assertInstanceOf('PG\JsonRpc\Exception\InvalidParams', $result) ;
        $this->assertEquals('id', $result->getId()) ;
    }

    /**
     * All business exceptions are supposed to be wrapped in InternalErrors
     */
    public function testExecuteBusinessException() {
        $call = new Call(
            $this->makeCall('Sample.throwBusiness', array(5)),
            array(
                'Sample' => 'PG\JsonRpc\tests\sample\Sample'
            ),
            $this->app
        );

        $result = $call->execute() ;

        $this->assertInstanceOf('PG\JsonRpc\ResultInterface', $result) ;
        $this->assertInstanceOf('PG\JsonRpc\Exception\InternalError', $result) ;
        $this->assertEquals('id', $result->getId()) ;
    }

    public function testExecuteLibraryException() {
        $call = new Call(
            $this->makeCall('Sample.throwLibrary', array(5)),
            array(
                'Sample' => 'PG\JsonRpc\tests\sample\Sample'
            ),
            $this->app
        );

        $result = $call->execute() ;

        $this->assertInstanceOf('PG\JsonRpc\ResultInterface', $result) ;
        $this->assertInstanceOf('PG\JsonRpc\Exception\ArgumentException', $result) ;
        $this->assertEquals('id', $result->getId()) ;
    }

    /**
     * @covers \PG\JsonRpc\Call
     */
    public function testParamsByNameSuccess() {
        // same order
        $call = new Call(
            $this->makeCall('Sample.divide', array('a' => 12, 'b' => 5)),
            array(
                'Sample' => 'PG\JsonRpc\tests\sample\Sample'
            ),
            $this->app
        );

        $result = $call->execute() ;
        $array = $result->toArray() ;

        $this->assertInstanceOf('PG\JsonRpc\Result', $result) ;

        $this->assertEquals($array['result'], 12/5) ;

        // inverted order, should still work
        $call = new Call(
            $this->makeCall('Sample.divide', array('b' => 5, 'a' => 12)),
            array(
                'Sample' => 'PG\JsonRpc\tests\sample\Sample'
            ),
            $this->app
        );

        $result = $call->execute() ;
        $array = $result->toArray() ;

        $this->assertInstanceOf('PG\JsonRpc\Result', $result) ;

        $this->assertEquals($array['result'], 12/5) ;
    }

    /**
     * @covers \PG\JsonRpc\Call
     */
    public function testParamsByNameFailure() {
        $call = new Call(
            $this->makeCall('Sample.divide', array('b' => 5, 'c' => 12)),
            array(
                'Sample' => 'PG\JsonRpc\tests\sample\Sample'
            ),
            $this->app
        );

        $result = $call->execute() ;
        $this->assertInstanceOf('PG\JsonRpc\ResultInterface', $result) ;
        $this->assertInstanceOf('PG\JsonRpc\Exception\InvalidParams', $result) ;
        $this->assertEquals('id', $result->getId()) ;
    }

    public function testInvalidMethod() {
        $call = new Call(
            $this->makeCall('Sample.divide.', array('a' => 12, 'b' => 5)),
            array(
                'Sample' => 'PG\JsonRpc\tests\sample\Sample'
            ),
            $this->app
        );

        $result = $call->execute() ;

        $this->assertInstanceOf('PG\JsonRpc\Exception\MethodNotFound', $result) ;
        $this->assertEquals('id', $result->getId()) ;
    }

    public function testUnkownScope() {
        $call = new Call(
            $this->makeCall('Unkown.divide', array('a' => 12, 'b' => 5)),
            array(
                'Sample' => 'PG\JsonRpc\tests\sample\Sample'
            ),
            $this->app
        );

        $result = $call->execute() ;

        $this->assertInstanceOf('PG\JsonRpc\Exception\MethodNotFound', $result) ;
        $this->assertEquals('id', $result->getId()) ;
    }

    public function testUnkownMethod() {
        $call = new Call(
            $this->makeCall('Sample.unkown', array('a' => 12, 'b' => 5)),
            array(
                'Sample' => 'PG\JsonRpc\tests\sample\Sample'
            ),
            $this->app
        );

        $result = $call->execute() ;

        $this->assertInstanceOf('PG\JsonRpc\Exception\MethodNotFound', $result) ;
        $this->assertEquals('id', $result->getId()) ;
    }

    public function testMethodIsPrivate() {
        $call = new Call(
            $this->makeCall('Sample.privateFunction', array('a' => 12, 'b' => 5)),
            array(
                'Sample' => 'PG\JsonRpc\tests\sample\Sample'
            ),
            $this->app
        );

        $result = $call->execute() ;

        $this->assertInstanceOf('PG\JsonRpc\Exception\MethodNotFound', $result) ;
        $this->assertEquals('id', $result->getId()) ;
    }

    /**
     * @covers \PG\JsonRpc\Call
     */
    public function testSanitizeSensitiveParams() {
        $logger = \Mockery::mock('Monolog\Logger') ;
        $logger
            ->shouldReceive('addDebug')
            ->once()
            ->with('Executing method.', array(
                'method' => 'PG\JsonRpc\tests\sample\Sample.withPassword',
                'params' => array(
                    '*REMOVED*',
                    'phpunit@example.com'
                )
            )) ;

        $app = new \Pimple() ;
        $app['logger'] = $logger ;

        $call = new Call(
            $this->makeCall('Sample.withPassword', array('should.be.hidden', 'phpunit@example.com')),
            array(
                'Sample' => 'PG\JsonRpc\tests\sample\Sample'
            ),
            $app
        );

        $call->execute() ;
    }

    /**
     * @covers \PG\JsonRpc\Call
     */
    public function testSanitizeArrays() {
        $logger = \Mockery::mock('Monolog\Logger') ;
        $logger
            ->shouldReceive('addDebug')
            ->once()
            ->with('Executing method.', array(
                'method' => 'PG\JsonRpc\tests\sample\Sample.withArray',
                'params' => array(
                    'Array'
                )
            )) ;

        $app = new \Pimple() ;
        $app['logger'] = $logger ;

        $call = new Call(
            $this->makeCall('Sample.withArray', array(array(1, 2, 3))),
            array(
                'Sample' => 'PG\JsonRpc\tests\sample\Sample'
            ),
            $app
        );

        $call->execute() ;
    }


}
 