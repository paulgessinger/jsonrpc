<?php

use PG\JsonRpc\Server ;

include __DIR__.'/vendor/autoload.php' ;

$server = new Server() ;
$server->run() ;
