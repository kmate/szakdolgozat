<?php

namespace fw\control;

use \fw\KeyValueStorage;
use \fw\view\TemplateView;

class HttpContext extends Context
{
    protected $_getParameters;
    protected $_postParameters;
    
    public function __construct()
    {
        $this->_getParameters  = new KeyValueStorage($_GET);
        $this->_postParameters = new KeyValueStorage($_POST);
    }
    
    public function getGetParameters()
    {
        return $this->_getParameters;
    }
    
    public function getPostParameters()
    {
        return $this->_postParameters;
    }
    
    public function setRouteInfo(RouteInfo $routeInfo)
    {
        $this->getView()->setTemplatesByRouteInfo($routeInfo);
        
        parent::setRouteInfo($routeInfo);
    }
    
    public function getView()
    {
        if (null == $this->_view)
        {
            $this->_view = new TemplateView($this->getConfiguration());
        }
        
        return parent::getView();
    }
    
    public function __get($propertyName)
    {
        switch ($propertyName)
        {
            case 'getParameters':
                return $this->getGetParameters();
            
            case 'postParameters':
                return $this->getPostParameters();
            
            default:
                return parent::__get($propertyName);
        }
    }
}