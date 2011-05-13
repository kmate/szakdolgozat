<?php

namespace fw\control\hooks;

use \fw\SessionManager;
use \fw\control\Context;
use \fw\control\RouteInfo;

abstract class AuthenticationHook implements ControllerHook
{
    const DEFAULT_LOGIN_CONTROLLER = 'default';
    const DEFAULT_LOGIN_ACTION     = 'login';
    
    protected $_loginControllerName;
    protected $_loginActionName;
    
    public function execute(Context $context)
    {
        $sessionName = $context->configuration->auth->get('session_name', '');
        
        SessionManager::start($sessionName);

        $this->_loginControllerName = $context->configuration->auth->get(
            'login_controller',
            self::DEFAULT_LOGIN_CONTROLLER
        );
        
        $this->_loginActionName = $context->configuration->auth->get(
            'login_action',
            self::DEFAULT_LOGIN_ACTION
        );
        
        if (!$this->_checkSkipValidation($context) && !$this->validateSession($context))
        {
            $context->frontController->forward(
                new RouteInfo($this->_loginControllerName, $this->_loginActionName)
            );
            
            SessionManager::destroy();
        }
    }
    
    protected function _checkSkipValidation(Context $context)
    {
        $controllerName = $context->routeInfo->getControllerName();
        $actionName     = $context->routeInfo->getActionName();
        
        $isPublicRoute = $context->configuration->auth->public
                                 ->get($controllerName)
                                 ->get($actionName, false);
        
        return (0 === strcmp($controllerName, $this->_loginControllerName)
             && 0 === strcmp($actionName,     $this->_loginActionName))
             || $isPublicRoute;
    }
    
    abstract public function validateSession(Context $context);
}