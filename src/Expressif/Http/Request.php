<?php
/**
 * Expressif- stream implementation
 * @author Ioan CHIRIAC
 * @license MIT
 */
namespace Expressif\Http {
  use Expressif\Socket\Connection;
  use Expressif\Stream\EventEmitter;

  class Request extends EventEmitter {
    protected $connection;
    private $buffer;
    private $body = false;
    public $cookies = [];
    public $headers = [];
    public $get = [];
    public $params = [];
    public $method;
    public $url;
    public $path;
    public $httpVersion;

    /**
     * Initialize a request handler
     */
    public function __construct(Connection $conn) {
      $this->connection = $conn;
      $this->connection->forward($this);
    }

    /**
     * Parsing the request (all the magic is here)
     */
    public function emit($event, array $args = []) {
      if ($event === 'data') {
        echo '<< ' . $args[0];
        $this->buffer .= $args[0];
        if ($this->body === false) {
          // headers are not already parsed
          if (false !== strpos($this->buffer, "\r\n\r\n")) {
            $this->body = true;
            $this->buffer = explode("\r\n\r\n", $this->buffer, 2);

            $parts = new Message($this->buffer[0]);
            $this->method = $parts->getRequestMethod();
            foreach($parts->getHeaders() as $k => $v) {
              $this->headers[strtolower($k)] = $v;
            }
            $this->url = $parts->getRequestUrl();
            $this->httpVersion = number_format($parts->getHttpVersion(), 1);
            $parts = explode('?', $this->url, 2);
            $this->path = $parts[0];
            if (!empty($parts[1])) {
              parse_str($parts[1], $this->params);
              parse_str($parts[1], $this->get);
            }
            if (!empty($this->headers['cookie'])) {
              $parts = http_parse_cookie($this->headers['cookie']);
              $this->cookies = $parts['cookies'];
            }
            if (!$this->method !== 'GET' && !empty($this->headers['content-length'])) {
              $this->body = intval($this->headers['content-length']);
              if ($this->body === 0) {
                $this->emit('ready');
              } else {
                $this->buffer = empty($this->buffer[1]) ? '' : $this->buffer[1];
              }
            } else {
              $this->body = 0;
              $this->emit('ready');
            }
          }
        } else {
          // parsing the request body
          if (strlen($this->buffer) ===  $this->body) {
            parse_str($this->buffer, $this->params);
            $this->emit('ready');
          }
        }
      }
      return parent::emit($event, $args);
    }
  }
}