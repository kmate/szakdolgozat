<?php

namespace fw\rpc\json;

class Response implements \fw\rpc\Response
{
    private $_result;
    private $_error;
    private $_messageId;
    
    public function __construct($result = null, Error $error = null, $messageId = null)
    {
        $this->setResult($result);
        $this->setError($error);
        $this->setMessageId($messageId);
    }
    
    public function getResult()
    {
        return $this->_result;
    }
    
    public function setResult($result)
    {
        if (null != $result)
        {
            $this->_error = null;
        }
        $this->_result = $result;
    }
    
    public function getError()
    {
        return $this->_error;
    }
    
    public function setError(Error $error = null)
    {
        if (null != $error)
        {
            $this->_result = null;
        }
        $this->_error = $error;
    }
    
    public function getMessageId()
    {
        return $this->_messageId;
    }
    
    public function setMessageId($messageId)
    {
        $this->_messageId = $messageId;
    }
    
    public function encode()
    {
        $responseData = new \stdClass();
        $responseData->jsonrpc = Constants::VERSION;
        
        if (null != $this->_error)
        {
            $error          = new \stdClass();
            $error->code    = (int)$this->_error->code;
            $error->message = (string)$this->_error->message;
            
            if (null != $this->_error->data)
            {
                $error->data = $this->_error->data;
            }
            
            $responseData->error = $error;
        }
        else if (null != $this->_result)
        {
            $responseData->result = $this->_result;
        }
        else
        {
            $this->setError(Error::create(Constants::INTERNAL_ERROR_CODE));
            
            return $this->encode();
        }
        
        $responseData->id = $this->_messageId;
        
        return json_encode($responseData);
    }
}