<?php

namespace fw\control;

use \fw\KeyValueStorage;
use \fw\view\View;

abstract class Context
{
    protected $_frontController;
    protected $_routeInfo;
    protected $_view;
    
    public function getRouteInfo()
    {
        return $this->_routeInfo;
    }
    
    public function setRouteInfo(RouteInfo $routeInfo)
    {
        $this->_routeInfo = $routeInfo;
    }
    
    public function getRouteParameters()
    {
        return $this->_routeInfo->getParameters();
    }
    
    public function getFrontController()
    {
        return $this->_frontController;
    }
    
    public function setFrontController(FrontController $frontController)
    {
        $this->_frontController = $frontController;
    }
    
    public function getConfiguration()
    {
        return $this->_frontController->getConfiguration();
    }
    
    public function getView()
    {
        return $this->_view;
    }
    
    public function setView(View $view)
    {
        $this->_view = $view;
    }
    
    public function __get($propertyName)
    {
        switch ($propertyName)
        {
            case 'routeInfo':
                return $this->getRouteInfo();
            
            case 'routeParameters':
                return $this->getRouteParameters();
            
            case 'frontController':
                return $this->getFrontController();
            
            case 'configuration':
                return $this->getConfiguration();
            
            case 'view':
                return $this->getView();
            
            default:
                return null;
        }
    }
}