<?php


namespace PG\JsonRpc;

use Monolog\Logger;
use PG\JsonRpc\Exception\InternalError;

/**
 * Groups multiple Results into one
 * using a JSON array, to match the spec.
 *
 * @package PG\JsonRpc
 */
class BatchResult extends AbstractResult {

    private $results ;

    /**
     * @var \Monolog\Logger
     */
    private $logger ;

    /**
     * @param Logger $logger
     * @param $results
     */
    public function __construct(Logger $logger, $results) {
        $this->results = $results ;
        $this->logger = $logger ;
    }

    /**
     * Accumulates the array outputs of all
     * containing results into one array.
     *
     * @return array
     * @throws Exception\InternalError
     */
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