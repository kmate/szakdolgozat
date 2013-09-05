<?php

namespace fw\rpc\json;

use \fw\Invoker;
use \fw\InvokerException;

/**
 * Távoli eljáráshívás kiszolgáló (JSON-RPC 2.0)
 * 
 * @author Karácsony Máté
 */
class Server extends \fw\rpc\Server
{
    const METHOD_PATTERN = '/^(?P<className>[a-zA-Z_][a-zA-Z0-9_]*)\.(?P<methodName>[a-zA-Z_][a-zA-Z0-9_]*)$/';
    
    /**
     * Kérés-objektum lekérdezése
     * 
     * @return Request
     */
    public function getRequest()
    {
        if (null == parent::getRequest())
        {
            $this->setRequest(new Request());
        }
        
        return parent::getRequest();
    }
    
    /**
     * Válasz-objektum lekérdezése
     * 
     * @return Response
     */
    public function getResponse()
    {
        if (null == parent::getResponse())
        {
            $this->setResponse(new Response());
        }
        
        return parent::getResponse();
    }
    
    /**
     * Kérés-objektum dekódolása és ellenőrzése
     * 
     * @param  string     a kérés átviteli formátumban
     * @return void
     * @throw  Exception  értelmezési hiba, vagy érvénytelen kérés
     */
    protected function _decodeRequest($rawData)
    {
        try
        {
            parent::_decodeRequest($rawData);
        }
        catch(\Exception $ex)
        {
            $this->getResponse()->setMessageId($this->getRequest()->getMessageId());
            
            throw $ex;
        }
        
        $this->getResponse()->setMessageId($this->getRequest()->getMessageId());
    }
    
    /**
     * Szolgáltatáshívás végrehajtása a kérés-ojektum felhasználásával
     * 
     * @return mixed      a hivás eredménye
     * @throw  Exception  hívás közben keletkezett kivétel
     */
    protected function _invokeService()
    {
        $methodExpression = $this->getRequest()->getMethod();
        
        if (1 === preg_match_all(self::METHOD_PATTERN, $methodExpression, $matches))
        {
            $className  = $matches['className'][0];
            $methodName = $matches['methodName'][0];
            
            $fullQualifiedClassName = $this->_namespace . '\\' . $className;
            
            if ('\\' !== substr($fullQualifiedClassName, 0, 1))
            {
                $fullQualifiedClassName = '\\' . $fullQualifiedClassName;
            }
            
            $parameters = $this->getRequest()->getParameters();
            
            return $this->_invokeServiceInternal($fullQualifiedClassName, $methodName, $parameters);
        }
        
        throw \fw\rpc\Exception::create(\fw\rpc\Exception::METHOD_NOT_FOUND);
    }
    
    private function _invokeServiceInternal($fullQualifiedClassName, $methodName, $parameters)
    {
        try
        {
            $invoker = new Invoker($fullQualifiedClassName);
            $invoker->checkInterface('\\fw\\rpc\\Service');
            $invoker->invoke('setConfiguration', array($this->_config));
            
            return $invoker->invoke($methodName, $parameters);
        }
        catch(InvokerException $ex)
        {
            switch ($ex->getCode())
            {
                case InvokerException::INVALID_IMPLEMENTATION:
                    $ex = \fw\rpc\Exception::create(\fw\rpc\Exception::INTERNAL_ERROR, $ex);
                    break;
                
                case InvokerException::MISSING_CLASS:
                case InvokerException::MISSING_METHOD:
                    $ex = \fw\rpc\Exception::create(\fw\rpc\Exception::METHOD_NOT_FOUND, $ex);
                    break;
                
                case InvokerException::INVALID_PARAMETERS:
                    $ex = \fw\rpc\Exception::create(\fw\rpc\Exception::INVALID_PARAMS, $ex);
                    break;
            }
            
            throw $ex;
        }
        catch(\Exception $ex)
        {
            throw \fw\rpc\Exception::create(\fw\rpc\Exception::INTERNAL_ERROR, $ex);
        }
    }
    
    /**
     * Szolgáltatáshívás eredményének beállítása a válasz-objektumon
     * 
     * @param  mixed  szolgáltatáshívás eredménye
     * @return void
     */
    protected function _applyInvokeResult($result)
    {
        $this->getResponse()->setResult($result);
    }
    
    /**
     * Kérés-objektum értelmezési hibáinak lekezelése
     * 
     * @return void
     */
    protected function _handleParseError()
    {
        $this->getResponse()->setError(Error::create(Constants::PARSE_ERROR_CODE));
    }
    
    /**
     * Kérés-objektum egyéb hibáinak lekezelése
     * 
     * @return void
     */
    protected function _handleInvalidRequest()
    {
        $this->getResponse()->setError(Error::create(Constants::INVALID_REQUEST_CODE));
    }
    
    /**
     * "Nem létező metódus" hiba lekezelése
     * 
     * @return void
     */
    protected function _handleMethodNotFound()
    {
        $this->getResponse()->setError(Error::create(Constants::METHOD_NOT_FOUND_CODE));
    }
    
    /**
     * "Érvénytelen hívási paraméterek" hiba lekezelése
     * 
     * @return void
     */
    protected function _handleInvalidParams()
    {
        $this->getResponse()->setError(Error::create(Constants::INVALID_PARAMS_CODE));
    }
    
    /**
     * Belső szolgáltatáshiba lekezelése
     * 
     * @return void
     */
    protected function _handleInternalError()
    {
        $this->getResponse()->setError(Error::create(Constants::INTERNAL_ERROR_CODE));
    }
}