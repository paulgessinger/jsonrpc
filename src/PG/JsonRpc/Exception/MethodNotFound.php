<?php


namespace PG\JsonRpc\Exception;


class MethodNotFound extends AbstractException {

    public function __construct($data = '', $message = 'Method not found', $code = -32601) {
        $this->message = $message ;
        $this->code = $code ;
        $this->data = $data ;
    }

} 