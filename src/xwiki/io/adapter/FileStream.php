<?php

namespace XWiki\IO\Adapter;

use XWiki\Exceptions\FileNotFoundException;
use XWiki\IO\IStream;

class FileStream implements IStream {

    private $source;

    const READ_FILE = 'r';
    const WRITE_FILE = 'w';
    const WRITE_FILE_OR_CREATE = 'w+';

    public function __construct($source, $type = FileStream::READ_FILE)
    {
        if(is_resource($source)) {
           $this->source = $source;
        } elseif (is_string($source)) {
            $this->source = fopen($source, $type);
        } else{
            throw new \InvalidArgumentException;
        }
    }

    public function write($buffer)
    {
        if(!is_resource($this->source)) {
            throw new \NullPointerException();
        }

        return fwrite($this->source, $buffer);
    }

    public function eof()
    {
        if(!is_resource($this->source))
            return null;
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
        if(!is_resource($this->source)) {
            throw new \NullPointerException;
        }

        return fwrite($this->source, $buffer, $bytes);
    }
}