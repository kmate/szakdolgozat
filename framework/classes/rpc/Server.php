<?php

namespace fw\rpc;

use \fw\ClassLoader;
use \fw\config\Configuration;

/**
 * Absztrakt távoli eljáráshívás kiszolgáló
 * 
 * @author Karácsony Máté
 */
abstract class Server
{
    const DEFAULT_CLASSPATH = __DIR__;
    const DEFAULT_NAMESPACE = '';
    
    protected $_classLoader;
    protected $_namespace;
    protected $_config;
    
    private $_request;
    private $_response;
    
    /**
     * Új kiszolgálót készít, mely a megadott konfiguráció alapján beállítja saját osztálybetöltőjét
     * (rpc.services.classpath és rpc.services.namespace beállítások alapján)
     * 
     * @param Configuration  konfiguráció
     */
    public function __construct(Configuration $config = null)
    {
        if (null == $config)
        {
            $config = new Configuration();
        }
        
        $this->_config = $config;
        
        $classPath        = $this->_config->rpc->services->get('classpath', self::DEFAULT_CLASSPATH);
        $this->_namespace = $this->_config->rpc->services->get('namespace', self::DEFAULT_NAMESPACE);
        
        $this->_classLoader = new ClassLoader($classPath, $this->_namespace);
        $this->_classLoader->register();
    }
    
    /**
     * Osztálybetöltő megszűntetése
     */
    public function __destruct()
    {
        $this->_classLoader->unregister();
        unset($this->_classLoader);
    }
    
    /**
     * Kérés-objektum lekérdezése
     * 
     * @return Request
     */
    public function getRequest()
    {
        return $this->_request;
    }
    
    /**
     * Kérés-objektum beállítása
     * 
     * @param  Request  az új kérés-objektum
     * @return void
     */
    public function setRequest(Request $request)
    {
        $this->_request = $request;
    }
    
    /**
     * Válasz-objektum lekérdezése
     * 
     * @return Response
     */
    public function getResponse()
    {
        return $this->_response;
    }
    
    /**
     * Válasz-objektum beállítása
     * 
     * @param  Response  az új válasz-objektum
     * @return void
     */
    public function setResponse(Response $response)
    {
        $this->_response = $response;
    }
    
    /**
     * Kiszolgálja a megadott kérést
     * 
     * @param  string  a kérés átviteli formátumban
     * @return string  a válasz átviteli formátumban
     */
    public function handle($rawData)
    {
        $decodeSuccessful = $this->_tryDecodeRequest($rawData);
        
        if ($decodeSuccessful)
        {
            $this->_tryInvokeService();
        }
        
        return $this->getResponse()->encode();
    }
    
    protected function _tryInvokeService()
    {
        try
        {
            $result = $this->_invokeService();
        }
        catch(Exception $ex)
        {
            switch ($ex->getCode())
            {
                case Exception::METHOD_NOT_FOUND:
                    $this->_handleMethodNotFound();
                    break;
                
                case Exception::INVALID_PARAMS:
                    $this->_handleInvalidParams();
                    break;
                
                case Exception::INTERNAL_ERROR:
                    $this->_handleInternalError();
                    break;
            }
            
            return;
        }
        
        $this->_applyInvokeResult($result);
    }
    
    protected function _tryDecodeRequest($rawData)
    {
        try
        {
            $this->_decodeRequest($rawData);
        }
        catch(Exception $ex)
        {
            switch ($ex->getCode())
            {
                case Exception::PARSE_ERROR:
                    $this->_handleParseError();
                    break;
                
                case Exception::INVALID_REQUEST:
                    $this->_handleInvalidRequest();
                    break;
            }
            
            return false;
        }
        
        return true;
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
        $request = $this->getRequest();
        $request->decode($rawData);
        
        if (!$request->isValid())
        {
            throw Exception::create(Exception::INVALID_REQUEST);
        }
    }
    
    /**
     * Szolgáltatáshívás végrehajtása a kérés-ojektum felhasználásával
     * 
     * @return mixed      a hivás eredménye
     * @throw  Exception  hívás közben keletkezett kivétel
     */
    abstract protected function _invokeService();
    
    /**
     * Szolgáltatáshívás eredményének beállítása a válasz-objektumon
     * 
     * @param  mixed  szolgáltatáshívás eredménye
     * @return void
     */
    abstract protected function _applyInvokeResult($result);
    
    /**
     * Kérés-objektum értelmezési hibáinak lekezelése
     * 
     * @return void
     */
    abstract protected function _handleParseError();
    
    /**
     * Kérés-objektum egyéb hibáinak lekezelése
     * 
     * @return void
     */
    abstract protected function _handleInvalidRequest();
    
    /**
     * "Nem létező metódus" hiba lekezelése
     * 
     * @return void
     */
    abstract protected function _handleMethodNotFound();
    
    /**
     * "Érvénytelen hívási paraméterek" hiba lekezelése
     * 
     * @return void
     */
    abstract protected function _handleInvalidParams();
    
    /**
     * Belső szolgáltatáshiba lekezelése
     * 
     * @return void
     */
    abstract protected function _handleInternalError();
}