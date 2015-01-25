<?php
/**
 * Expressif- stream implementation
 * @author Ioan CHIRIAC
 * @license MIT
 */
namespace Expressif\Http {
  use Expressif\Socket\Connection;
  use Expressif\Stream\EventEmitter;

  class Response extends EventEmitter {
    protected $connection;
    protected $headersSent = false;
    protected $chunked = false;

    public $headers = ['X-Powered-By' => 'Expressif'];
    public $statusCode = 200;

    /**
     * Initialize a response handler
     */
    public function __construct(Connection $conn) {
      $this->connection = $conn;
      $this->connection->forward($this);
    }

    /**
     * Sets headers
     */
    public function writeHead($code = 200, array $headers = []) {
      if ($this->headersSent) {
        throw new \Exception('Headers are already sent !');
      }
      $this->statusCode = $code;
      $this->headers = array_merge($this->headers, $headers);
      return $this;
    }

    /**
     * Generates headers and consider them sent
     */
    protected function getHttpHeaders() {
      $this->headersSent = true;
      if (empty($this->headers['Content-Length'])) {
        $this->headers['Transfer-Encoding'] = 'chunked';
        $this->chunked = true;
      }
      $status = (int) $this->statusCode;
      $text = isset(Status::$text[$status]) ? Status::$text[$status] : '';
      $header = "HTTP/1.1 $status $text\r\n";
      foreach($this->headers as $name => $value) {
        $name = strtr($name, "\r\n", '');
        $value = strtr($value, "\r\n", '');
        $header .= "$name: $value\r\n";
      }
      return $header . "\r\n";
    }

    /**
     * Writes some data
     */
    public function write($chunk) {
      if (!$this->headersSent) {
        $this->connection->send($this->getHttpHeaders());
       }
      if ($this->chunked) {
        $chunk = dechex(strlen($chunk)) . "\r\n$chunk\r\n";
      }
      $this->connection->write($chunk);
      return $this;
    }
    /**
     * Sets/Unsets an header
     * @return response
     */
    public function setHeader($name, $value) {
      if ($this->headersSent) {
        throw new \Exception('Headers are already sent !');
      }
      if ($value === null) {
        unset($this->headers[$name]);
      } else {
        $this->headers[$name] = $value;
      }
      return $this;
    }
    /**
     * Send data and end the connection
     */
    public function end($data = null) {
      $this->connection->on('write', function() {
        $this->connection->close();
      });
      $this->write($data);
      if ($this->chunked && !empty($data)) {
        $this->connection->write("0\r\n\r\n");
      }
      return $this;
    }
  }
}