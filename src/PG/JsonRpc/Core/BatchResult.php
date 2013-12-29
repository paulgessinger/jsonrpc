<?php


namespace PG\JsonRpc\Core;

use Monolog\Logger;
use PG\JsonRpc\Exception\InternalError;

class BatchResult extends AbstractResult {

    private $results ;

    /**
     * @var \Monolog\Logger
     */
    private $logger ;

    public function __construct(Logger $logger, $results) {
        $this->results = $results ;
        $this->logger = $logger ;
    }

    public function toArray() {
        $combined = array() ;

        foreach($this->results as $result) {
            if(!($result instanceof ResultInterface)) {
                $this->logger->addCritical('A returned result did not implement ResultInterface') ;
                throw new InternalError() ; // this should not happen
            }
            $combined[] = $result->toArray() ;
        }

        return $combined ;
    }

} 