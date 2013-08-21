<?php

namespace XWiki\Http;

use XWiki\Exceptions\IOReadException;
use XWiki\Exceptions\NotAllowedException;

class Response implements \ArrayAccess{
    private $stream;
    private $statusCode;
    private $headers = array();

    public function __construct($stream) {
        $this->stream = $stream;
        $this->getStatusCode();
        $this->getHeaders();
    }

    private function getStatusCode() {
        if(!is_resource($this->stream)) {
            throw new \NullPointerException;
        }

        $fistLine = fgets($this->stream);
        if (!$fistLine) {
            throw new IOReadException("Can't read");
        }
        $matches = array();
        if(preg_match('/^(HTTP\/\d+.\d+) (?P<code>\d+)/', $fistLine, $matches)) {
            $this->statusCode = (int)$matches['code'];
        }
    }

    private function getHeaders() {
        if (!is_resource($this->stream)) {
            throw new \NullPointerException;
        }

        while(!feof($this->stream)) {
            $line = fgets($this->stream);
            if (!$line) {
                throw new IOReadException("Can't read");
            }
            if ($line === "\r\n") {
                break;
            }
            list($name, $value) = explode(":", $line);
            $this->headers[trim($name)] = trim($value);
        }
    }

    public function getStream() {
        return is_resource($this->stream)?$this->stream:null;
    }

    public function isOk() {
        return $this->statusCode === 200;
    }

    public function offsetExists($offset)
    {
        return isset($this->headers[$offset]);
    }

    public function offsetGet($offset)
    {
        return $this->headers[$offset];
    }

    public function offsetSet($offset, $value)
    {
        throw new NotAllowedException;
    }

    public function offsetUnset($offset)
    {
        throw new NotAllowedException;
    }
}