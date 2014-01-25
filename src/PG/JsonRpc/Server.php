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
class Server extends \Pimple {

    public static $debug = false ;

    private $exposed = array() ;


    /**
     * Constructs the object. You can inject a logger instance here, for
     * the lib to use.
     *
     * Also registers shutdown function and an exception handler to make sure
     * all errors that could occur are provided to the client in JSON format
     * with varying debug information.
     *
     * @param bool $debug
     */
    function __construct($debug = false) {
		ob_start() ;

        self::$debug = $debug ;

        $this['logger'] = function($c) {
            $logger = new Logger('jsonrpc') ;
            $logger->pushHandler(new NullHandler()) ;
            return $logger ;
        } ;

		// register shutdown function so that we can report parse errors and such to the client.
		register_shutdown_function(array($this, 'handleShutdown'));
        set_error_handler(array($this, 'handleError')) ;

        set_exception_handler(array($this, 'handleException')) ;
	}

    public function handleError($code, $msg, $file, $line) {
        $allowed = array(
            E_USER_WARNING,
            E_USER_NOTICE,
            E_NOTICE,
            E_WARNING
        ) ;

        if(in_array($code, $allowed)) {
            return ;
        }
        else {
            // this seems to be a serious error, print error response and exit.
            ob_clean() ;

            throw new InternalError('Internal error', -32603, ($msg.' in '.$file.' on line #'.$line)) ;
        }
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
            try {
                $call = new Call($c, $this->exposed, $this) ;
                $results[] = $call->execute() ;
            }
            catch(AbstractException $e) {
                if(isset($c['id'])) {
                    $e->setId($c['id']) ;
                }

                $results[] = $e ;
            }

        }

        if(!$request->isBatch()) {
            $response->setResult($results[0]) ;
        }
        else {
            $response->setResult(new BatchResult($this, $results)) ;
        }

        return $response ;
    }
}
