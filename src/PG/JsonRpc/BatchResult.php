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

    private $app ;

    /**
     * @param Server $server
     * @param $results
     */
    public function __construct(\Pimple $app, $results) {
        $this->results = $results ;
        $this->app = $app ;
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
                $this->app['logger']->addCritical('A returned result did not implement ResultInterface') ;
                throw new InternalError() ; // this should not happen
            }
            $combined[] = $result->toArray() ;
        }

        return $combined ;
    }

} 