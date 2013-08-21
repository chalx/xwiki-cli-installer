<?php

namespace XWiki\IO;

class DataReadStream
{
    private $stream;

    public function __construct(IStream $stream) {
        if($stream === null) {
            throw new \NullPointerException;
        }
        $this->stream = $stream;
    }

    public function getLine() {
        return $this->getLine();
    }

    public function getBytes($bytes) {
        if(!is_numeric($bytes)) {
            throw new \InvalidArgumentException();
        }

        return $this->stream->getBytes($bytes);
    }

    public function eof() {
        return $this->stream->eof();
    }
}