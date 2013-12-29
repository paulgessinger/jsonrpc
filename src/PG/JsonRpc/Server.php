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
		register_shutdown_function(function() { 
		    $error = error_get_last(); 
		    if(isset($error['type'])) {
				ob_clean() ;

				$response = new Response() ;
                $response->setResult(
                    new InternalError('Internal error', -32603, ($error['message'].' in '.$error['file'].' on line #'.$error['line']))
                ) ;
                $response->send() ;

				exit();
			}
		});

        set_exception_handler(function(AbstractException $e) {
            ob_clean() ;

            $response = new Response() ;
            $response->setResult($e) ;
            $response->send() ;

            exit();
        }) ;
	}

    public function run() {

        $request = Request::createFromGlobals() ;
        $response = $this->handle($request) ;
        $response->send() ;

    }

    public function expose($name, $class) {
        if(!class_exists($class)) {
            throw new ClassNotExists($class) ;
        }

        $this->exposed[$name] = $class ;
    }

    /**
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
