<?php

namespace json;

use \fw\config\Configuration;
use \fw\rpc\Service;

class TestService implements Service
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
    
    public function sendBack($message)
    {
        return $message;
    }
    
    public function returnConfig()
    {
        return $this->getConfiguration();
    }
}