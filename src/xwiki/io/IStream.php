<?php

namespace XWiki\IO;

interface IStream {
    public function __construct($source);
    public function write($buffer);
    public function eof();
    public function getLine();
    public function getBytes($bytes);
    public function writeBytes($buffer, $bytes);
}