<?php


namespace PG\JsonRpc;


use PG\Common\JSON;

abstract class AbstractResult implements ResultInterface {

    /**
     * @return string
     */
    public function toJSON() {
        return JSON::encode($this->toArray(), Server::$debug) ;
    }
}