<?php


namespace PG\JsonRpc\Exception;


use PG\JsonRpc\Server;
use PG\Common\JSON;
use PG\JsonRpc\ResultInterface;

abstract class AbstractException extends \Exception implements ResultInterface {
    protected $code ;
    protected $message ;
    protected $data ;

    /**
     * @param null $data
     */
    public function setData($data)
    {
        $this->data = $data;
    }

    /**
     * @return null
     */
    public function getData()
    {
        return $this->data;
    }

    public function __construct($message = null, $code = null, $data = null) {
        $this->message = $message ;
        $this->code = $code ;
        $this->data = $data ;
    }

    /**
     * @return string
     */
    public function toJSON() {
        return JSON::encode($this->toArray(), Server::$debug) ;
    }

    public function toArray() {
        $result = array(
            'code' => $this->code,
            'message' => $this->message
        ) ;

        if(Server::$debug) {
            $result['data'] = $this->data ;
        }

        return $result ;
    }

} 