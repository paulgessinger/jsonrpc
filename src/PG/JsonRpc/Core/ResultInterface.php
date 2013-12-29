<?php


namespace PG\JsonRpc\Core;


interface ResultInterface {
    /**
     * @return string
     */
    public function toJSON() ;

    /**
     * @return array
     */
    public function toArray() ;
} 