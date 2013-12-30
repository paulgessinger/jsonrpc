#JSONRPC for PHP

Allows you to write simple and fast JSON RPC APIs by exposing arbitrary objects to HTTP POST requests.

## Installation
By all means; use Composer!

1. Get [Composer](http://getcomposer.org/)
2. Add `"paulgessinger/jsonrpc": "dev-master"` to your require
3. Install dependencies with `composer install`


## Getting started


Put something like this in your bootstrap file (e.g. index.php).

```php
use PG\JsonRpc\Server ;

include __DIR__.'/vendor/autoload.php' ;

$server = new Server() ;

server->expose('Sample', 'PG\JsonRpc\tests\sample\Sample') ;

$server->run() ;
```

You can then do POST requests on it with a JSON body in the form of

```json
{
	"jsonrpc":"2.0",
	"id":1,
	"method":"Sample.divide",
	"params": [11, 5]
}
```
(documented in the [JSON RPC specification](http://www.jsonrpc.org/specification)).


while *Sample* looks like

```php
namespace PG\JsonRpc\tests\sample;
use PG\JsonRpc\Exception\ArgumentException;

class Sample {
    public function divide($a, $b) {
        return $a/$b ;
    }
	
	// ...
}
```

You will get a reponsse in the form of

```json
{
	"jsonrpc":"2.0",
	"id":1,
	"result":2.2
}
```

Have fun.

## Tests

The lib tries to be fully unit tested. Go take it for a spin, go to the lib's root directory and run `phpunit`. 
PHPUnit is also included in the *require-dev*s so you can get it with `composer install --dev` or `composer update`.

## Contributors
- [Paul Gessinger](http://paulgessinger.com)

## License 

The MIT License (MIT)

Copyright (c) 2013 Paul Gessinger

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.