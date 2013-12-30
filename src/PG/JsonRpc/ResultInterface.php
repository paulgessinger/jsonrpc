<?php


namespace PG\JsonRpc;

/**
 * Basic structure of what a Result must be capable of.
 * @package PG\JsonRpc
 */
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