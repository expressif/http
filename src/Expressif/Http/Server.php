<?php
/**
 * Expressif- stream implementation
 * @author Ioan CHIRIAC
 * @license MIT
 */
namespace Expressif\Http {

  use Expressif\Socket\Server as SocketServer;

  /**
   * Http Server
   */
  class Server extends SocketServer {
    /**
     * Accept the incomming connection and bind http handlers
     */
    public function accept() {
      $connection = parent::accept();
      $request = new Request($connection);
      $request->on('ready', function() use($request, $connection) {
        $this->emit('request', array(
          $request, new Response($connection)
        ));
      });
      return $connection;
    }
  }
}