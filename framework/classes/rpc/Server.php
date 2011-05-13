<?php

namespace fw\rpc;

use \fw\ClassLoader;
use \fw\config\Configuration;

abstract class Server
{
    const DEFAULT_CLASSPATH = __DIR__;
    const DEFAULT_NAMESPACE = '';
    
    protected $_classLoader;
    protected $_namespace;
    protected $_config;
    
    private $_request;
    private $_response;
    
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
    
    public function __destruct()
    {
        $this->_classLoader->unregister();
        unset($this->_classLoader);
    }
    
    public function getRequest()
    {
        return $this->_request;
    }
    
    public function setRequest(Request $request)
    {
        $this->_request = $request;
    }
    
    public function getResponse()
    {
        return $this->_response;
    }
    
    public function setResponse(Response $response)
    {
        $this->_response = $response;
    }
    
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
    
    protected function _decodeRequest($rawData)
    {
        $request = $this->getRequest();
        $request->decode($rawData);
        
        if (!$request->isValid())
        {
            throw Exception::create(Exception::INVALID_REQUEST);
        }
    }
    
    abstract protected function _invokeService();
    
    abstract protected function _applyInvokeResult($result);
    
    abstract protected function _handleParseError();
    abstract protected function _handleInvalidRequest();
    abstract protected function _handleMethodNotFound();
    abstract protected function _handleInvalidParams();
    abstract protected function _handleInternalError();
}