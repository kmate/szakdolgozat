<?php

namespace fw\view;

use fw\control\RouteInfo;

abstract class View
{
    protected $_rendered   = false;
    protected $_autoRender = true;
    
    public function setAutoRender($value)
    {
        $this->_autoRender = $value;
    }
    
    public function runAutoRenderer()
    {
        if ($this->_autoRender && !$this->_rendered)
        {
            $this->render();
        }
    }
    
    public function render($returnsOutput = false)
    {
        $this->_rendered = true;
        
        return $this->_render($returnsOutput);
    }
    
    public function generateLink($controllerName, $actionName, array $parameters = array())
    {
        return ViewUtils::generateLink($controllerName, $actionName, $parameters);
    }
    
    public function getBaseHref()
    {
        return ViewUtils::getBaseHref();
    }
    
    abstract protected function _render($returnsOutput);
}