<?php

namespace PG\JsonRpc\Exception ;

class InvalidParams extends AbstractException {
    public function __construct($data = '', $message = 'Invalid params', $code = -32602) {
        $this->message = $message ;
        $this->code = $code ;
        $this->data = $data ;
    }
}