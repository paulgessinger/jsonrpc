<?php


namespace PG\JsonRpc\tests\sample;


use PG\JsonRpc\Exception\ArgumentException;

class Sample {

    /**
     * @param float $a
     * @param float $b
     * @return float
     */
    public function divide($a, $b) {
        return $a/$b ;
    }

    /**
     * @throws SampleException
     */
    public function throwBusiness() {
        throw new SampleException() ;
    }

    /**
     * @throws \PG\JsonRpc\Exception\ArgumentException
     */
    public function throwLibrary() {
        throw new ArgumentException('ArgumentException') ;
    }

    /**
     * @param array $params
     * @return int
     */
    public function divideName(array $params) {
        return $params['a'] / $params['b'] ;

    }

    /**
     *
     */
    private function privateFunction() {
        return 'hallo' ;
    }

    /**
     * @param $_pwd
     * @param $email
     * @return int
     */
    public function withPassword($_pwd, $email) {
        return 1 ;
    }

    /**
     * @param array $array
     * @return int
     */
    public function withArray(array $array) {
        return 1 ;
    }

} 