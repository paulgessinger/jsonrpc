<?php

namespace PG\JsonRpc ;

use Symfony\Component\HttpFoundation\Response as HttpResponse ;

/**
 * Based on Symfony's HttpFoundation\Response. Only adds a few methods,
 * and touches nothing the Class itself does.
 *
 * @package PG\JsonRpc
 */
class Response extends HttpResponse {

    /**
     * Sets the Content-Type header to applicatioin/json on construction of
     * every response object. We always want to return JSON.
     *
     * @param string $content
     * @param int $status
     * @param array $headers
     */
    public function __construct($content = '', $status = 200, $headers = array()) {
        parent::__construct($content, $status, $headers) ;

        $this->headers->add(array('Content-Type' => 'application/json')) ;
    }

    /**
     * Sets the content of this response to the JSON output
     * of a Result object.
     *
     * @param ResultInterface $result
     */
    public function setResult(ResultInterface $result) {
        $this->setContent($result->toJSON()) ;
    }
}