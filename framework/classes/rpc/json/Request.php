<?php

namespace fw\rpc\json;

class Request implements \fw\rpc\Request
{
    private $_version;
    private $_method;
    private $_parameters;
    private $_messageId;
    
    public function __construct(
        $version          = Constants::VERSION,
        $method           = '',
        array $parameters = array(),
        $messageId        = null,
        $isBatch          = false
    )
    {
        $this->_version    = $version;
        $this->_method     = $method;
        $this->_parameters = $parameters;
        $this->_messageId  = $messageId;
        
        $this->_isBatch  = $isBatch;
    }
    
    public function getVersion()
    {
        return $this->_version;
    }
    
    public function getMethod()
    {
        return $this->_method;
    }
    
    public function getParameters()
    {
        return $this->_parameters;
    }
    
    public function getMessageId()
    {
        return $this->_messageId;
    }
    
    public function isValid()
    {
        return Constants::VERSION === $this->_version
            && is_string($this->_method)
            && !empty($this->_method)
            && is_array($this->_parameters);
    }
    
    public function decode($rawData)
    {
        if ('[]' === $rawData)
        {
            $this->_fillByEmptyRequest();
        }
        else
        {
            $decodedData = json_decode($rawData);
            
            if (null == $decodedData)
            {
                $this->_fillByEmptyRequest();
                
                throw \fw\rpc\Exception::create(\fw\rpc\Exception::PARSE_ERROR);
            }
            
            if (is_array($decodedData))
            {
                $this->_fillByEmptyRequest();
            }
            else
            {
                $this->_fillByRequestData($decodedData);
            }
        }
    }
    
    private function _fillByRequestData($requestData)
    {
        $this->_version    = isset($requestData->jsonrpc) ? $requestData->jsonrpc : null;
        $this->_method     = isset($requestData->method)  ? $requestData->method  : null;
        $this->_messageId  = isset($requestData->id)      ? $requestData->id      : null;
        $this->_parameters = $this->_parseParameters($requestData);
    }
    
    private function _fillByEmptyRequest()
    {
        $this->_fillByRequestData(new \stdClass());
    }
    
    private function _parseParameters($requestData)
    {
        if (isset($requestData->params))
        {
            if (is_object($requestData->params))
            {
                return get_object_vars($requestData->params);
            }
            else if (is_array($requestData->params))
            {
                return $requestData->params;
            }
            else
            {
                return null;
            }
        }
        else
        {
            return array();
        }
    }
}