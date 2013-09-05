<?php

namespace fw\rpc\json;

/**
 * Távoli eljáráshívási válasz (JSON-RPC 2.0)
 * 
 * @author Karácsony Máté
 */
class Response implements \fw\rpc\Response
{
    private $_result;
    private $_error;
    private $_messageId;
    
    /**
     * Új válasz-objektumot hoz létre a megadott tartalommal
     * 
     * @param mixed   eljáráshívás eredménye
     * @param Error   hiba-objektum
     * @param string  üzenet-azonosító
     */
    public function __construct($result = null, Error $error = null, $messageId = null)
    {
        $this->setResult($result);
        $this->setError($error);
        $this->setMessageId($messageId);
    }
    
    /**
     * Eredmény lekérdezése
     * 
     * @return mixed
     */
    public function getResult()
    {
        return $this->_result;
    }
    
    /**
     * Eredmény beállítása
     * 
     * @param  mixed  az új eredmény
     * @return void
     */
    public function setResult($result)
    {
        if (null != $result)
        {
            $this->_error = null;
        }
        $this->_result = $result;
    }
    
    /**
     * Hiba-objektum lekérdezése
     * 
     * @return Error
     */
    public function getError()
    {
        return $this->_error;
    }
    
    /**
     * Hiba-objektum beállítása
     * 
     * @param  Error  az új hiba-objektum
     * @return void
     */
    public function setError(Error $error = null)
    {
        if (null != $error)
        {
            $this->_result = null;
        }
        $this->_error = $error;
    }
    
    /**
     * Üzenet-azonosító lekérdezése
     * 
     * @return string
     */
    public function getMessageId()
    {
        return $this->_messageId;
    }
    
    /**
     * Üzenet-azonosító beállítása
     * 
     * @param  string  az új üzenet-azonosító
     * @return void
     */
    public function setMessageId($messageId)
    {
        $this->_messageId = $messageId;
    }
    
    /**
     * Válasz kódolása a átviteli formátumra (JSON)
     * 
     * @return string
     */
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