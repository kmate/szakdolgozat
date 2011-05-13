<?php

namespace fw\control;

use \fw\config\Configuration;

abstract class Router
{
    const DEFAULT_CONTROLLER = 'default';
    const DEFAULT_ACTION     = 'index';
    const ERROR_CONTROLLER   = 'error';
    const EXCEPTION_ACTION   = 'exception';
    const NOT_FOUND_ACTION   = 'not-found';
    
    protected $_config;
    protected $_defaultController;
    protected $_defaultAction;
    protected $_errorController;
    protected $_exceptionAction;
    protected $_notFoundAction;
    
    public function __construct(Configuration $config = null)
    {
        if (null == $config)
        {
            $config = new Configuration();
        }
        
        $this->setConfiguration($config);
    }
    
    public function setConfiguration(Configuration $config)
    {
        $this->_config = $config;
        
        $this->_defaultController = $this->_config->router->get('default_controller', self::DEFAULT_CONTROLLER);
        $this->_defaultAction     = $this->_config->router->get('default_action',     self::DEFAULT_ACTION);
        $this->_errorController   = $this->_config->router->get('error_controller',   self::ERROR_CONTROLLER);
        $this->_exceptionAction   = $this->_config->router->get('exception_action',   self::EXCEPTION_ACTION);
        $this->_notFoundAction    = $this->_config->router->get('not_found_action',   self::NOT_FOUND_ACTION);
    }
    
    abstract public function parseRoute($route = null);
    
    abstract public function generateRoute(RouteInfo $routeInfo);
    
    abstract public function redirect(RouteInfo $routeInfo);
    
    public function getDefaultController()
    {
        return $this->_defaultController;
    }
    
    public function getDefaultAction()
    {
        return $this->_defaultAction;
    }
    
    public function getErrorController()
    {
        return $this->_errorController;
    }
    
    public function getExceptionAction()
    {
        return $this->_exceptionAction;
    }
    
    public function getNotFoundAction()
    {
        return $this->_notFoundAction;
    }
}
