<?php

namespace fw\control;

use \fw\KeyValueStorage;

/**
 * Útválasztási információ
 * 
 * @author Karácsony Máté
 */
class RouteInfo
{
    private $_controllerName;
    private $_actionName;
    private $_parameters;
    
    /**
     * Új útválasztási információ létrehozása
     * 
     * @param string  vezérlő-név
     * @param string  akció-név
     * @param mixed   paraméterek (asszociatív tömb vagy kulcs-érték tár)
     */
    public function __construct($controllerName, $actionName, $parameters = array())
    {
        $this->_controllerName = preg_replace('/[^a-zA-Z0-9\-]/', '', strtolower($controllerName));
        $this->_actionName     = preg_replace('/[^a-zA-Z0-9\-]/', '', strtolower($actionName));
        
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
    
    /**
     * Vezérlő-név lekérdezése
     * 
     * @return string
     */
    public function getControllerName()
    {
        return $this->_controllerName;
    }
    
    /**
     * Akció-név lekérdezése
     * 
     * @return string
     */
    public function getActionName()
    {
        return $this->_actionName;
    }
    
    /**
     * Paraméterek lekérdezése
     * 
     * @return string
     */
    public function getParameters()
    {
        return $this->_parameters;
    }
}