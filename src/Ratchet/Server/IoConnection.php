<?php

namespace Ratchet\Server;

use GuzzleHttp\Psr7\Request;
use Ratchet\ConnectionInterface;
use React\Socket\ConnectionInterface as ReactConn;

/**
 * {@inheritdoc}
 */
class IoConnection implements ConnectionInterface
{
    /**
     * @var \React\Socket\ConnectionInterface
     */
    protected $conn;

    /**
     * @var Request
     */
    public $httpRequest;

    /**
     * @param \React\Socket\ConnectionInterface $conn
     */
    public function __construct(ReactConn $conn)
    {
        $this->conn = $conn;
    }

    /**
     * {@inheritdoc}
     */
    public function send($data)
    {
        $this->conn->write($data);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function close()
    {
        $this->conn->end();
    }

    /**
     * @return Request
     */
    public function getHttpRequest()
    {
        return $this->httpRequest;
    }

    /**
     * @param Request $httpRequest
     */
    public function setHttpRequest(Request $httpRequest)
    {
        $this->httpRequest = $httpRequest;
    }
}
