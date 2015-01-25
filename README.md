# http

Contains http bindings between pecl_http and libevent for creating a fast web
server.

## Requirements

This library use expressif/stream and http module :

- PHP 5.6+
- libevent
- pecl_http

If you are on windows (for dev & test purpose) you can dirrectly [install pre-built libraries](https://github.com/expressif/win-dist).

---

*NOTE :* if you plan to use this library on a multi-core server, you should consider
[expressif/cluster](https://github.com/expressif/cluster)

## usage

```php
<?php

  require 'vendor/autoload.php';
  use Expressif\Http\Server;

  $endpoint = '127.0.0.1:1337';
  echo "Serving HTTP at http://$endpoint\n";

  $http = new Server('tcp://' . $endpoint);

  $http->on('request', function($req, $res) {
    $res->writeHead(200, ['Content-Type' => 'text/plain']);
    $res->end('Hello world');
  });

```
