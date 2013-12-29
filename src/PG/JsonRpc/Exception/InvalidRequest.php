<?php

namespace PG\JsonRpc\Exception ;

class InvalidRequest extends AbstractException {
    public function __construct($message = 'Invalid Request', $code = -32600, $data = '') {
        $this->message = $message ;
        $this->code = $code ;
        $this->data = $data ;
    }
}