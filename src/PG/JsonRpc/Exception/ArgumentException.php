<?php
namespace PG\JsonRpc\Exception ;

/**
 * Class ArgumentException
 * @package PG\JsonRpc\Exception
 */
class ArgumentException extends InvalidParams {
	function __construct($message, $id = null) {
		parent::__construct($message, 'Invalid params', -32602, $id) ;
	}
}
