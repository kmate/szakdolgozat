<?php

namespace fw\log;

class OutputTarget extends LogTarget
{
    public function write($level, $source = '', $message = '')
    {
        echo $this->format($level, $source, $message) . PHP_EOL;
    }
}