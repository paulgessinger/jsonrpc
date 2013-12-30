<?php

namespace PG\JsonRpc\Exception ;

/**
 * Respresents the "Internal request" from spec.
 * @package PG\JsonRpc\Exception
 */
class InvalidRequest extends AbstractException {

    /**
     * Slight reordering of arguments from abstract.
     *
     * @param string $message
     * @param null $code
     * @param string $data
     * @param null $id
     */
    public function __construct($message = 'Invalid Request', $code = -32600, $data = '', $id = null) {
        $this->message = $message ;
        $this->code = $code ;
        $this->data = $data ;
        $this->id = null ;
    }
}