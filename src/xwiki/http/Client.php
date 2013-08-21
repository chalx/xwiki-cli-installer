<?php

namespace XWiki\Http;

use XWiki\Exceptions\ConnectionException;
use XWiki\Exceptions\IOReadException;
use XWiki\Exceptions\IOWriteException;

class Client
{
    private $socketStream;
    private $errno;
    private $errstr;
    private $address = array();
    private $timeout = 10;
    private $connectedHost;
    private $port;
    private $options = array();

    public function __construct($host = null, $port = 80, array $options = array('content-type' => "text/html"))
    {
        $this->connectedHost = $host;
        $this->port = $port;
        $this->options = $options;
        $this->extract($host);
        $this->connect();
    }

    /**
     * @param $host
     * @throws \InvalidArgumentException
     */
    private function extract($host)
    {
        if (strlen(trim($host)) === 0 || empty($host)) {
            throw new \InvalidArgumentException("Host cannot be empty");
        }

        $this->address['resource'] = '/';
        $this->address['domain'] = (strpos($host, "://")) ? substr($host, (strpos($host, "://") + 3)) : $host;
        if (($start = strpos($this->address['domain'], '/'))) {
            if ($start === strlen($this->address['domain']) - 1) {
                $this->address['domain'] = substr($this->address['domain'], 0, strlen($this->address['domain']) - 1);
            } else {
                $temp = $this->address['domain'];
                $this->address['domain'] = substr($temp, 0, $start);
                $this->address['resource'] = substr($temp, $start);
            }
        }
    }

    private function connect()
    {
        $addr = "tcp://" . $this->address['domain'] . ":" . $this->port;
        $this->socketStream = stream_socket_client($addr, $this->errno, $this->errstr, $this->timeout, STREAM_CLIENT_CONNECT | STREAM_CLIENT_PERSISTENT);
        if ($this->socketStream === false) {
            throw new ConnectionException(sprintf("[%d]: [%s]", $this->errno, $this->errstr));
        }
    }

    public function send()
    {
        $headers = $this->getHeaders();
        if ( !fwrite($this->socketStream, $headers) ) {
            throw new IOWriteException("Can't write");
        }
        return $this;
    }

    public function setHost($host, $port = 80)
    {
        if ($this->connectedHost != $host || $this->port != $port) {
            if (is_resource($this->socketStream)) {
                $this->close();
            }
            $this->extract($host);
            $this->port = $port;
            $this->connect();
        }

        return $this;
    }

    public function close()
    {
        if (is_resource($this->socketStream)) {
            fclose($this->socketStream);
        }
        $this->socketStream = null;
        $this->connectedHost = null;
    }

    public function getResponse()
    {
        try{
            return new Response($this->socketStream);
        }catch(IOReadException $ex) {
            $this->close();
            throw $ex;
        } catch(\NullPointerException $ex) {
            $this->close();
            throw $ex;
        }
    }

    private function getHeaders()
    {
        $header  = "GET %s HTTP/1.1\r\n";
        $header .= "Host: %s\r\n";
        $header .= "Content-type: %s\r\n";
        $header .= "Accept: */*\r\n\r\n";
        return sprintf($header, $this->address['resource'], $this->address['domain']. ":" . $this->port, $this->options['content-type']);
    }

    public function __destruct() {
        $this->close();
    }
}