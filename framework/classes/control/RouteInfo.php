<?php

namespace fw\control;

use \fw\KeyValueStorage;

class RouteInfo
{
    private $_controllerName;
    private $_actionName;
    private $_parameters;
    
    public function __construct($controllerName, $actionName, $parameters = array())
    {
        $this->_controllerName = $controllerName;
        $this->_actionName     = $actionName;
        
        if ($parameters instanceof KeyValueStorage)
        {
            $this->_parameters = $parameters;
        }
        else if (is_array($parameters))
        {
            $this->_parameters = new KeyValueStorage($parameters);
        }
        else
        {
            $this->_parameters = new KeyValueStorage();
        }
    }
    
    public function getControllerName()
    {
        return $this->_controllerName;
    }
    
    public function getActionName()
    {
        return $this->_actionName;
    }
    
    public function getParameters()
    {
        return $this->_parameters;
    }
}