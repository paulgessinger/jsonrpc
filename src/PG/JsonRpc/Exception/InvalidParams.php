<?php

namespace PG\JsonRpc\Exception ;

/**
 * Respresents the "Internal params" from spec.
 * @package PG\JsonRpc\Exception
 */
class InvalidParams extends AbstractException {

    /**
     * Slight reordering of arguments from abstract.
     *
     * @param string $data
     * @param string $message
     * @param null $code
     * @param null $id
     */
    public function __construct($data = '', $message = 'Invalid params', $code = -32602, $id = null) {
        $this->message = $message ;
        $this->code = $code ;
        $this->data = $data ;
        $this->id = $id ;
    }
}