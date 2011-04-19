<?php

namespace fw\config;

abstract class FileBasedConfiguration extends Configuration
{
    public function __construct($filePath)
    {
        $this->_checkFileIsReadable($filePath);
        
        $filePath = realpath($filePath);
        
        if (false !== ($result = $this->_parseFile($filePath)))
        {
            parent::__construct($this->_parseIntoArray($result), true);
        }
        else
        {
            $this->_throwParseException($filePath, $result);
        }
    }
    
    abstract protected function _parseFile($filePath);
    abstract protected function _parseIntoArray($parserResult);
    
    private function _checkFileIsReadable($filePath)
    {
        if (!is_readable($filePath))
        {
            throw new Exception(
                'Unable to load configuration file: \'' . $filePath . '\'',
                Exception::UNABLE_TO_READ_FILE
            );
        }
    }
    
    private function _throwParseException($filePath, $parseResult)
    {
        throw new Exception(
            'Unable to parse configuration file: \'' . $filePath . '\'',
            Exception::UNABLE_TO_PARSE_FILE
        );
    }
}