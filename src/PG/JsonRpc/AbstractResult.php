<?php


namespace PG\JsonRpc;


use PG\Common\JSON;

/**
 * Base class for results encapsulating data to be returned
 * @package PG\JsonRpc
 */
abstract class AbstractResult implements ResultInterface {

    /**
     * Builds the response JSON object.
     *
     * @return string
     */
    public function toJSON() {
        return JSON::encode($this->toArray(), Server::$debug) ;
    }
}