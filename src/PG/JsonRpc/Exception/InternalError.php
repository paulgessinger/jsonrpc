<?php

namespace PG\JsonRpc\Exception ;

/**
 * Respresents the "Internal error" from spec.
 * @package PG\JsonRpc\Exception
 */
class InternalError extends AbstractException {

    /**
     * Slight reordering of arguments from abstract.
     *
     * @param string $message
     * @param null $code
     * @param string $data
     * @param null $id
     */
    public function __construct($message = 'Internal error', $code = -32603, $data = '', $id = null) {
        $this->message = $message ;
        $this->code = $code ;
        $this->data = $data ;
        $this->id = $id ;
    }
	
}