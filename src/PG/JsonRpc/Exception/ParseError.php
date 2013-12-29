<?php


namespace PG\JsonRpc\Exception;


class ParseError extends AbstractException {

    public function __construct($data = null, $message = 'Parse error', $code = -32700) {
        parent::__construct($message, $code, $data) ;
    }

}