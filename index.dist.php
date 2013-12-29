<?php

use PG\JsonRpc\Core\Server ;

include __DIR__.'/vendor/autoload.php' ;

$server = new Server() ;
$server->run() ;
