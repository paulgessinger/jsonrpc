<?php


namespace PG\JsonRpc;


class NullResult implements ResultInterface {

    /**
     * @return string
     */
    public function toJSON() {
        return '' ;
    }

    /**
     * @return array
     */
    public function toArray() {
        return array() ;
    }
}