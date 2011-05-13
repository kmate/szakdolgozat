<?php

use \fw\config\Configuration;

class TestService implements \fw\rpc\Service
{
    private $_config;
    
    public function getConfiguration()
    {
        return $this->_config;
    }
    
    public function setConfiguration(Configuration $config)
    {
        $this->_config = $config;
    }
    
    public function throwException()
    {
        throw new \Exception();
    }
    
    public function arrayOnly(array $parameter) {}
}