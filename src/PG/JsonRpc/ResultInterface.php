<?php


namespace PG\JsonRpc;


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