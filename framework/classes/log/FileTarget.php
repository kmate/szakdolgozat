<?php

namespace fw\log;

class FileTarget extends LogTarget
{
    private $_handle;
    
    public function __construct($filePath, $formatString = LogTarget::DEFAULT_FORMAT)
    {
        if (false === ($this->_handle = @fopen($filePath, 'a')))
        {
            throw new FileException(
                'Unable to open log file for writing: \'' . $filePath . '\'',
                FileException::UNABLE_TO_OPEN
            );
        }
        
        parent::__construct($formatString);
    }
    
    public function __destruct()
    {
        if ($this->_handle)
        {
            fclose($this->_handle);
        }
    }
    
    public function write($level, $source = '', $message = '')
    {
        fwrite($this->_handle, $this->format($level, $source, $message) . PHP_EOL);
    }
}