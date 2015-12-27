<?php
/**
 * Expressif- stream implementation
 * @author Ioan CHIRIAC
 * @license MIT
 */
namespace Expressif\Http {

	class Message {

		protected $method;
		protected $url;
		protected $type;
		protected $version;
		protected $headers = array();

		public function __construct($headers) {
			$headers = explode("\r\n", $headers);
			$protocol = explode(' ', $headers[0]);
			$this->method = $protocol[0];
			$this->url = $protocol[1];
			$mode = explode('/', $protocol[2]);
			$this->type = $mode[0];
			$this->version = $mode[1];
			for($i = 1, $size = count($headers); $i < $size; $i++) {
				$header = explode(': ', $headers[$i], 2);
				$this->headers[$header[0]] = $header[1];
			}
		}

		public function getRequestMethod() {
			return $this->method;
		}

		public function getRequestUrl() {
			return $this->url;
		}

		public function getHttpVersion() {
			return $this->version;
		}

		public function getHeaders() {
			return $this->headers;
		}
	}

}
