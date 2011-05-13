<?php

namespace fw\view;

use \fw\control\Router;
use \fw\control\RouteInfo;

class ViewUtils
{
    private static $_router;
    
    public static function setRouter(Router $router)
    {
        self::$_router = $router;
    }
    
    public static function generateLink($controllerName, $actionName, array $parameters = array())
    {
        $routeInfo = new RouteInfo($controllerName, $actionName, $parameters);
        
        return $_SERVER['SCRIPT_NAME'] . self::$_router->generateRoute($routeInfo);
    }
    
    public static function getBaseHref()
    {
        $host = !empty($_SERVER['HTTP_HOST'])
              ? $_SERVER['HTTP_HOST']
              : $host = $_SERVER['SERVER_NAME'] . ':' . $_SERVER['SERVER_PORT'];
        
        $protocol = (isset($_SERVER['HTTPS']) && 'on' == $_SERVER['HTTPS'] ? 'https' : 'http');
        
        return $protocol . '://' . $host . str_replace('index.php', '', $_SERVER['SCRIPT_NAME']);
    }
}