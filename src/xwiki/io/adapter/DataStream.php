<?php

namespace XWiki\IO\Adapter;

use XWiki\IO\IStream;

class DataStream implements IStream {
    private $source;

    public function __construct($source)
    {
        if(!is_resource($source) || !strstr(get_resource_type($source), 'stream')) {
            throw new \InvalidArgumentException;
        }
        $this->source = $source;
    }

    public function write($buffer)
    {
        // TODO: Implement write() method.
    }

    public function eof()
    {
        if(!is_resource($this->source)) {
            return null;
        }

        return feof($this->source);
    }

    public function getLine()
    {
        return $this->eof()?null:fgets($this->source);
    }

    public function getBytes($bytes)
    {
        return $this->eof()?null:fread($this->source, $bytes);
    }

    public function writeBytes($buffer, $bytes)
    {
        // TODO: Implement writeBytes() method.
    }
}