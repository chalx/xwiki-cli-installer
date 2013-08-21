<?php

namespace XWiki\IO;
use NullPointerException;

class DataWriteStream {
    private $stream;

    public function __construct(IStream $stream) {
        if ( $stream === null ) {
            throw new \NullPointerException;
        }
        $this->stream = $stream;
    }

    public function write($buffer) {
        $this->stream->write($buffer);
    }

    public function writeBytes($buffer, $bytes) {
        return $this->stream->writeBytes($buffer, $bytes);
    }

    public function writeStream(DataReadStream $stream, \Closure $func = null) {
        if ($stream === null) {
            throw new NullPointerException("You must provide a stream");
        }

        $totalWrite = 0;
        $blockSize = 1048576;
        while(!$stream->eof()) {
            $write = $this->writeBytes($stream->getBytes($blockSize), $blockSize);
            $totalWrite = $totalWrite + $write;
            if ($func !== null) {
                call_user_func($func, $totalWrite);
            }
        }
    }
}