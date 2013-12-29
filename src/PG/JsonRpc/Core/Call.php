<?php

namespace PG\JsonRpc\Core ;


use Monolog\Logger;
use PG\JsonRpc\Exception\AbstractException;
use PG\JsonRpc\Exception\InternalError;
use PG\JsonRpc\Exception\InvalidParams;
use PG\JsonRpc\Exception\InvalidRequest;
use PG\JsonRpc\Exception\MethodNotFound;

class Call {

	protected $version ;
	protected $method ;
	protected $params ;
	protected $id ;
    private $logger ;
    private $exposed = array() ;

	function __construct($object, array $exposed, Logger $logger) {

        $this->logger = $logger ;
        $this->exposed = $exposed ;

		$this->validateRequest($object) ;

		$this->version = $object['jsonrpc'] ;
		$this->method = $object['method'] ;
		$this->params = $object['params'] ;
		$this->id = $object['id'] ;

	}

	function getVersion() {
		return $this->version ;
	}

	function getMethod() {
		return $this->method ;
	}

	function getParams() {
		return $this->params ;
	}

	function getId() {
		return $this->id ;
	}

	protected function validateRequest($object) {
		if(!is_array($object)) {
			throw new InvalidRequest ;
		}

		if(
			   !array_key_exists('jsonrpc', $object)
			|| $object['jsonrpc'] !== '2.0'
			|| !array_key_exists('method', $object)
			|| empty($object['method'])
			|| !array_key_exists('params', $object)
			|| !array_key_exists('id', $object)
		) {
			throw new InvalidRequest() ;
		}

        if(!is_array($object['params'])) {
            throw new InvalidParams('Parameters must be an array') ;
        }
	}

    /**
     * @param array $params
     * @return \PG\JsonRpc\Core\ResultInterface
     */
    public function execute() {
        try {
            $method = $this->resolveMethod($this->getMethod()) ;
            $result = $this->callMethod($method, $this->getParams()) ;

            return new Result($this->getId(), $result) ;
        }
        catch(AbstractException $e ) {
            return $e ;
        }
        catch(\Exception $e) { // catch EVERYTHING, is this a good idea?
            return new InternalError('Internal error', -32603, $e->__toString()) ;
        }
    }

    private function sanitizeParameters($params, $method_params) {
        $sanitized_parameters = array() ;

        for($i=0;$i<count($params);$i++) {
            if(substr($method_params[$i]->getName(), 0, 1) === '_') {
                // sanitize this one
                $sanitized_parameters[$i] = '*REMOVED*' ;

                continue;
            }

            if(is_array($params[$i])) {
                $sanitized_parameters[$i] = 'Array' ;
                continue;
            }

            $sanitized_parameters[$i] = $params[$i] ;
        }

        return $sanitized_parameters ;
    }

    private function callMethod($method, $params) {
        $reflection = new \ReflectionMethod(get_class($method[0]), $method[1]) ;

        $method_parameters = $reflection->getParameters() ;

        /*
         * Sanitize Params
         * * exclude params prefixed with _
         * * dont include arrays
         */

        // skip if not developing, info is not used anywhere else
        if(Server::$debug) {
            // sanitize parameters: if order of params is correct and everything matches, only remove
            // params marked as sensitive, such as pwd. In prod we dont log anything at all here.

            $sanitized_parameters = $this->sanitizeParameters($params, $method_parameters) ;
            $this->logger->addDebug('Executing method.', array('method' => get_class($method[0]).'.'.$method[1], 'params' => $sanitized_parameters)) ;
        }

        if(count($method_parameters) === 0) {
            return call_user_func($method) ;
        }

        if($method_parameters[0]->getName() === 'params' && count($method_parameters) === 1) {
            // method wants passing by name

            if(array_keys($params) === range(0, count($params) - 1)) {
                // call is by order => invalid
                throw new InvalidParams('method call with params by order, params by name expected.') ;
            }

            // call also is by name => good to go
            return call_user_func_array($method, array($params)) ;

        } else {
            // method wants passing by order

            if(array_keys($params) !== range(0, count($params) - 1)) {
                // call is by name => invalid
                throw new InvalidParams('method call with params by name, params by order expected.') ;
            }

            $mandatory_parameters = 0 ;

            foreach($method_parameters as $method_parameter) {
                if(!$method_parameter->isOptional()) {
                    $mandatory_parameters++;
                }
            }

            if(count($params) < $mandatory_parameters) {
                throw new InvalidParams('Mandatory parameters are missing from the call.') ;
            }

            // call also is by order => good to go
            return call_user_func_array($method, $params) ;
        }
    }

    private function resolveMethod($method_string) {
        $method_array = explode('.', $method_string) ;

        if(count($method_array) !== 2) {
            throw new InvalidRequest('Invalid method') ;
        }

        $class = $method_array[0] ;
        $class = str_replace('../', '', $class) ;
        $method = $method_array[1] ;

        if(!array_key_exists($class, $this->exposed)) {
            throw new MethodNotFound('Scope '.$class.' was not found.') ;
        }

        $real_class = $this->exposed[$class] ;

        $reflection = new \ReflectionClass($real_class) ;

        if(!$reflection->hasMethod($method)) {

            throw new MethodNotFound('Method '.$method.' in scope '.$class.' was not found.') ;

        }

        $reflection_method = $reflection->getMethod($method) ;

        if(!$reflection_method->isPublic()) {
            throw new MethodNotFound('Method '.$method.' in scope '.$class.' is not public (this is probably a bug in server software).') ;
        }

        $callable = array(new $real_class, $method) ;

        if(!is_callable($callable)) {
            throw new MethodNotFound('Method '.$method.' in scope '.$class.' is not callable (this is probably a bug in server software).') ;
        }

        // if we got until everything should be fine
        return $callable ;
    }

}