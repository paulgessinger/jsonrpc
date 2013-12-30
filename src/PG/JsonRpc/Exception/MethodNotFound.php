<?php


namespace PG\JsonRpc\Exception;

/**
 * Respresents the "Method not found" from spec.
 * @package PG\JsonRpc\Exception
 */
class MethodNotFound extends AbstractException {

    /**
     * Slight reordering of arguments from abstract.
     *
     * @param string $data
     * @param string $message
     * @param null $code
     * @param null $id
     */
    public function __construct($data = '', $message = 'Method not found', $code = -32601, $id = null) {
        $this->message = $message ;
        $this->code = $code ;
        $this->data = $data ;
        $this->id = $id ;
    }

} 