<?php

namespace PG\JsonRpc\Exception ;

class InternalError extends AbstractException {

    public function __construct($message = 'Internal error', $code = -32603, $data = '') {
        $this->message = $message ;
        $this->code = $code ;
        $this->data = $data ;
    }
	
}