<?php


namespace PG\JsonRpc\Exception;

/**
 * Respresents the "parse error" from spec.
 * @package PG\JsonRpc\Exception
 */
class ParseError extends AbstractException {

    /**
     * Slight reordering of arguments from abstract.
     *
     * @param null $data
     * @param string $message
     * @param null $code
     */
    public function __construct($data = null, $message = 'Parse error', $code = -32700) {
        parent::__construct($message, $code, $data) ;
    }

}