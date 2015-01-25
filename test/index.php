<?php

require __DIR__ . '/../vendor/autoload.php';
use Expressif\Http\Server;

$endpoint = '127.0.0.1:1337';
echo "Serving HTTP at http://$endpoint\n";

$http = new Server('tcp://' . $endpoint);

$http->on('request', function($req, $res) {
  $res->writeHead(200, ['Content-Type' => 'text/plain']);
  $res->end('Hello world');
});