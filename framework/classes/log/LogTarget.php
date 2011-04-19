<?php

namespace fw\log;

use \fw\Utils;

abstract class LogTarget
{
    const DEFAULT_FORMAT = '%t - [%l] %s: %m (%h)';
    
    public function __construct($formatString = self::DEFAULT_FORMAT)
    {
        $this->_formatString = $formatString;
    }
    
    abstract public function write($level, $source = Log::UNKNOWN_SOURCE, $message = '-');
    
    public function format($level, $source = Log::UNKNOWN_SOURCE, $message = '-')
    {
        $replacements = array(
            '%l' => $level,
            '%s' => $source,
            '%m' => $message,
            '%t' => date('d/M/Y:H:i:s O'),
            '%h' => Utils::getClientIp()
        );
        
        return strtr($this->_formatString, $replacements);
    }
}