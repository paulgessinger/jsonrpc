<?php
namespace PG\JsonRpc\Exception ;

class ArgumentException extends InvalidParams {
	function __construct($message) {
		parent::__construct($message) ;
	}
}
