<?php


namespace PG\JsonRpc;

/**
 * Encapsulates a single Result obtained from a business method call.
 * @package PG\JsonRpc
 */
class Result extends AbstractResult {
    private $jsonrpc = '2.0' ;
    private $result ;
    private $id ;

    /**
     * @param $id
     * @param $result
     */
    public function __construct($id, $result) {

        $this->id = $id ;
        $this->result = $result ;

    }

    /**
     * @return array
     */
    public function toArray() {
        return array(
            'jsonrpc' => $this->jsonrpc,
            'result' => $this->result,
            'id' => $this->id
        ) ;
    }
}