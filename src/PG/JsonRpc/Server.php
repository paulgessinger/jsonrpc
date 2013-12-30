<?php
namespace PG\JsonRpc ;

use Monolog\Handler\NullHandler;
use Monolog\Logger;
use PG\JsonRpc\Exception\AbstractException;
use PG\JsonRpc\Exception\ClassNotExists;
use PG\JsonRpc\Exception\InternalError;

/**
 * Base Singleton for the REST frame
 *
 * @package ccjsonrpcapi
 * @author Paul Gessinger
 */
class Server {

    public static $debug = false ;

    private $logger  ;
    private $exposed = array() ;

    public function getLogger() {
        return $this->logger ;
    }

    /**
     * Constructs the object. You can inject a logger instance here, for
     * the lib to use.
     *
     * Also registers shutdown function and an exception handler to make sure
     * all errors that could occur are provided to the client in JSON format
     * with varying debug information.
     *
     * @param bool $debug
     * @param Logger $logger
     */
    function __construct($debug = false, Logger $logger = null) {
		ob_start() ;

        self::$debug = $debug ;

        if($logger === null) {
            $this->logger = new Logger('jsonrpc') ;
            $this->logger->pushHandler(new NullHandler()) ;

        }
        else {
            $this->logger = $logger ;
        }

		// register shutdown function so that we can report parse errors and such to the client.
		register_shutdown_function(array($this, 'handleShutdown'));

        set_exception_handler(array($this, 'handleException')) ;
	}

    public function handleShutdown() {
        $error = error_get_last();
        if(isset($error['type'])) {
            ob_clean() ;

            $this->handleException(
                new InternalError('Internal error', -32603, ($error['message'].' in '.$error['file'].' on line #'.$error['line']))
            ) ;

            //exit();
        }
    }

    public function handleException(AbstractException $e) {
        ob_clean() ;

        $response = new Response() ;
        $response->setResult($e) ;
        $response->send() ;

        // exit();
    }

    /**
     * Runs the current request obtained from globals.
     */
    public function run() {

        $request = Request::createFromGlobals() ;
        $response = $this->handle($request) ;
        $response->send() ;

    }

    /**
     * Exposes a class. All public members of that class become
     * callable through JSON RPC. Mark everything you want to hide
     * as private/protected.
     *
     * Class members are not exposed.
     *
     * @param $name
     * @param $class
     * @throws Exception\ClassNotExists
     */
    public function expose($name, $class) {
        if(!class_exists($class)) {
            throw new ClassNotExists($class) ;
        }

        $this->exposed[$name] = $class ;
    }

    /**
     * Handles a given Request object and returns
     * a Response object which can then be sent to the
     * client.
     *
     * @param Request $request
     * @return Response
     */
    public function handle(Request $request) {

        $calls = $request->extract() ;

        $response = new Response() ;
        $results = array() ;

        foreach($calls as $c) {
            $call = new Call($c, $this->exposed, $this->logger) ;
            $results[] = $call->execute() ;
        }

        if(count($results) === 1) {
            $response->setResult($results[0]) ;
        }
        else {
            $response->setResult(new BatchResult($this->logger, $results)) ;
        }

        return $response ;
    }
}
