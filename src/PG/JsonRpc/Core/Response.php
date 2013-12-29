<?php

namespace PG\JsonRpc\Core ;

use Symfony\Component\HttpFoundation\Response as HttpResponse ;

class Response extends HttpResponse {

    public function __construct($content = '', $status = 200, $headers = array()) {
        parent::__construct($content, $status, $headers) ;

        $this->headers->add(array('Content-Type' => 'application/json')) ;
    }

    public function setResult(ResultInterface $result) {
        $this->setContent($result->toJSON()) ;
    }
}