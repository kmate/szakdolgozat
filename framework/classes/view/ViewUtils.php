<?php

namespace fw\view;

use \fw\control\Router;
use \fw\control\RouteInfo;

/**
 * Nézet-segédfüggvények
 * 
 * @author Karácsony Máté
 */
class ViewUtils
{
    private static $_router;
    
    /**
     * Beállítja a link-generáláshoz használt útválasztót
     *
     * @param  Router a link-generáláshoz használandó útválasztó
     * @return void
     */
    public static function setRouter(Router $router)
    {
        self::$_router = $router;
    }
    
    /**
     * Linket generál egy vezérlő megadott akciójához, a kivánt paraméterekkel
     *
     * @param  string  vezérlő neve
     * @param  string  vezérlő-akció neve
     * @param  array   paraméterek
     * @return string  a generált link
     */
    public static function generateLink($controllerName, $actionName, array $parameters = array())
    {
        $routeInfo = new RouteInfo($controllerName, $actionName, $parameters);
        
        return $_SERVER['SCRIPT_NAME'] . self::$_router->generateRoute($routeInfo);
    }
    
    /**
     * Lekéri az alkalmazás relatív hivatkozásainak viszonyítási pontjául szolgáló URL-t
     *
     * @return string
     */
    public static function getBaseHref()
    {
        $host = !empty($_SERVER['HTTP_HOST'])
              ? $_SERVER['HTTP_HOST']
              : $host = $_SERVER['SERVER_NAME'] . ':' . $_SERVER['SERVER_PORT'];
        
        $protocol = (isset($_SERVER['HTTPS']) && 'on' == $_SERVER['HTTPS'] ? 'https' : 'http');
        
        return $protocol . '://' . $host . str_replace('index.php', '', $_SERVER['SCRIPT_NAME']);
    }
}