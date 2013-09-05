<?php

namespace fw\control;

use \fw\Utils;
use \fw\config\Configuration;

/**
 * URL-alapú útválasztó
 * 
 * @author Karácsony Máté
 */
class UrlRouter extends Router
{
    /**
     * Információk kinyerése URL-ből
     * 
     * @param  string     URL
     * @return RouteInfo  útvonal információ
     */
    public function parseRoute($route = null)
    {
        if (null === $route)
        {
            $route = isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : '';
        }
        
        $this->_trimRoute($route);
        
        $routeParts = (0 < strlen($route))     ? explode('/', $route)     : array();
        $controller = (0 < count($routeParts)) ? array_shift($routeParts) : $this->_defaultController;
        $action     = (0 < count($routeParts)) ? array_shift($routeParts) : $this->_defaultAction;
        $parameters = $this->_parseParameters($routeParts);
        
        if (0 === strcmp($this->getErrorController(), $controller) &&
            0 === strcmp($this->getExceptionAction(), $action))
        {
            $action = $this->getNotFoundAction();
        }
        
        return new RouteInfo($controller, $action, $parameters);
    }
    
    /**
     * URL létrehozása útvonal információból
     * 
     * @param  RouteInfo  útvonal információ
     * @return string     URL
     */
    public function generateRoute(RouteInfo $routeInfo)
    {
        $route  = '/' . $routeInfo->getControllerName();
        $route .= '/' . $routeInfo->getActionName();
        
        $parameters = $routeInfo->getParameters()->toArray();
        
        foreach ($parameters as $parameterName => $parameterValue)
        {
            if (is_array($parameterValue))
            {
                foreach ($parameterValue as $itemValue)
                {
                    $route .= '/' . $parameterName . '/' . $itemValue;
                }
            }
            else
            {
                $route .= '/' . $parameterName . '/' . $parameterValue;
            }
        }
        
        return $route;
    }
    
    /**
     * Átirányítás útvonal információ alapján
     * 
     * @param  RouteInfo  cél-útvonal információ
     * @return void
     */
    public function redirect(RouteInfo $routeInfo)
    {
        $prefix = isset($_SERVER['SCRIPT_NAME']) ? $_SERVER['SCRIPT_NAME'] : '';
        
        Utils::setLocation($prefix . $this->generateRoute($routeInfo));
    }
    
    private function _trimRoute(&$route)
    {
        if (0 === strpos($route, '/'))
        {
            $route = substr($route, 1);
        }
        
        if (strlen($route) - 1 === strrpos($route, '/'))
        {
            $route = substr($route, 0, -1);
        }
    }
    
    private function _parseParameters(&$routeParts)
    {
        $parameters = array();
        
        while (0 < count($routeParts))
        {
            $parameterName  = array_shift($routeParts);
            $parameterValue = array_shift($routeParts);
            
            $this->_addParameter($parameterName, $parameterValue, $parameters);
        }
        
        return $parameters;
    }
    
    private function _addParameter($parameterName, $parameterValue, &$parameters)
    {
        if (!isset($parameters[$parameterName]))
        {
            $parameters[$parameterName] = $parameterValue;
        }
        else
        {
            $this->_extractArrayParameter($parameterName, $parameterValue, $parameters);
        }
    }
    
    private function _extractArrayParameter($parameterName, $parameterValue, &$parameters)
    {
        if (!is_array($parameters[$parameterName]))
        {
            $parameters[$parameterName] = array($parameters[$parameterName]);
        }
        
        $parameters[$parameterName][] = $parameterValue;
    }
}