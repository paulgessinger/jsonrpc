JSONRPC for PHP
===============

Allows you to write simple and fast JSON RPC APIs by exposing arbitrary objects to HTTP POST requests.

Installation
------------

Getting started
---------------

Put something like this in your bootstrap file (e.g. index.php).

```php
use PG\JsonRpc\Core\Server ;

include __DIR__.'/vendor/autoload.php' ;

$server = new Server() ;

$server->expose('PublicName', 'Reference\To\Your\Class') ;

$server->run() ;
´´´

You can then do POST requests on it with a JSON body in the form of

```json
{
	"jsonrpc":"2.0",
	"id":1,
	"method":"PublicName.someMethod",
	"params": ["parameter"]
}
```

(documented in the [JSON RPC specification](http://www.jsonrpc.org/specification)) and receive according responses.

Have fun.
