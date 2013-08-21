<?php

namespace XWiki\Helper;

class Progress {
    private $output;
    private $lastStep = 0;
    private $barWidth;
    private $redrawFreq = 1;

    public function __construct() {
        $this->output = fopen("php://stdout", "w");
    }

    public function start($barWidth = 20) {
        $this->barWidth = $barWidth;
        $data = str_repeat('-', $barWidth);
        $this->overwrite($data);
    }

    public function advance($step = 1, $add = true) {
        if($this->lastStep == $this->barWidth) {
            return;
        }

        if ( $add ) {
            $this->lastStep += $step;
        } else {
            $this->lastStep = $step;
        }
        $data  = str_repeat('=', $this->lastStep);

        if($this->barWidth - $this->lastStep !== 0 ) {
            $data .= '>';
        }

        if ( 0 <= $this->barWidth - $this->lastStep - 1) {
            $data .= str_repeat('-', $this->barWidth - $this->lastStep - 1);
        }

        if(0 === $this->lastStep % $this->redrawFreq) {
            $this->overwrite($data);
        }
    }

    public function getProgress() {
        return sprintf("[%d/%d]", $this->lastStep, $this->barWidth);
    }

    public function finish() {
        $data = str_repeat('=', $this->barWidth);
        $this->overwrite($data);
        echo "\n";
        fclose($this->output);
    }

    public function overwrite($data)
    {
        fwrite($this->output, "\x0D");
        $data = sprintf('[%s]', $data);
        $progress = $this->getProgress();
        fwrite($this->output, str_pad($data,strlen($data) + strlen($progress), $progress, STR_PAD_RIGHT));
        fflush($this->output);
    }
}