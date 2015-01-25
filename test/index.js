var http = require('http');

// Create an HTTP server
http.createServer(function (req, res) {
  res.writeHead(200, {'Content-Type': 'text/plain'});
  res.end('Hello world');
}).listen(1337, '127.0.0.1');

console.log("Serving HTTP at http://127.0.0.1:1337/");