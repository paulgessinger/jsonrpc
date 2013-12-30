<?php


namespace PG\JsonRpc\Exception;


use PG\JsonRpc\Server;
use PG\Common\JSON;
use PG\JsonRpc\ResultInterface;

abstract class AbstractException extends \Exception implements ResultInterface {
    protected $code ;
    protected $message ;
    protected $data ;
    protected $id ;

    public function setId($id) {
        $this->id = $id ;
    }

    public function getId() {
        return $this->id ;
    }

    public function __construct($message = null, $code = null, $data = null, $id = null) {
        $this->message = $message ;
        $this->code = $code ;
        $this->data = $data ;
        $this->id = $id ;
    }

    /**
     * @return string
     */
    public function toJSON() {
        return JSON::encode($this->toArray(), Server::$debug) ;
    }

    public function toArray() {
        $result = array(
            'id' => $this->id,
            'jsonrpc' => '2.0',
            'code' => $this->code,
            'message' => $this->message
        ) ;

        if(Server::$debug) {
            $result['data'] = $this->data ;
        }

        return $result ;
    }

} 