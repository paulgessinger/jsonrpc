<?php


namespace PG\JsonRpc;

class Result extends AbstractResult {
    private $jsonrpc = '2.0' ;
    private $result ;
    private $id ;

    public function __construct($id, $result) {

        $this->id = $id ;
        $this->result = $result ;

    }

    public function toArray() {
        return array(
            'jsonrpc' => $this->jsonrpc,
            'result' => $this->result,
            'id' => $this->id
        ) ;
    }
}