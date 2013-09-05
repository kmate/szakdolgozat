<?php

namespace fw\control;

use \fw\config\Configuration;

/**
 * Útválasztók ősosztálya
 * 
 * @author Karácsony Máté
 */
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
    
    /**
     * Útválasztó létrehozása
     * 
     * @param Configuration  konfiguráció
     */
    public function __construct(Configuration $config = null)
    {
        if (null == $config)
        {
            $config = new Configuration();
        }
        
        $this->setConfiguration($config);
    }
    
    /**
     * Konfiguráció beállítása
     * Az alábbi beállítások kerülnek feldolgozásra:
     * - router.default_controller
     * - router.default_action
     * - router.error_controller
     * - router.exception_action
     * - router.not_found_action
     * 
     * @param  Configuration  konfiguráció
     * @return void
     */
    public function setConfiguration(Configuration $config)
    {
        $this->_config = $config;
        
        $this->_defaultController = $this->_config->router->get('default_controller', self::DEFAULT_CONTROLLER);
        $this->_defaultAction     = $this->_config->router->get('default_action',     self::DEFAULT_ACTION);
        $this->_errorController   = $this->_config->router->get('error_controller',   self::ERROR_CONTROLLER);
        $this->_exceptionAction   = $this->_config->router->get('exception_action',   self::EXCEPTION_ACTION);
        $this->_notFoundAction    = $this->_config->router->get('not_found_action',   self::NOT_FOUND_ACTION);
    }
    
    /**
     * Információk kinyerése nyers útvonalból
     * 
     * @param  string     nyers útvonal
     * @return RouteInfo  útvonal információ
     */
    abstract public function parseRoute($route = null);
    
    /**
     * Nyers útvonal létrehozása útvonal információból
     * 
     * @param  RouteInfo  útvonal információ
     * @return string     nyers útvonal
     */
    abstract public function generateRoute(RouteInfo $routeInfo);
    
    /**
     * Átirányítás útvonal információ alapján
     * 
     * @param  RouteInfo  cél-útvonal információ
     * @return void
     */
    abstract public function redirect(RouteInfo $routeInfo);
    
    /**
     * Alapértelmezett vezérlő nevének lekérdezése
     * 
     * @return string
     */
    public function getDefaultController()
    {
        return $this->_defaultController;
    }
    
    /**
     * Alapértelmezett vezérlő-akció nevének lekérdezése
     * 
     * @return string
     */
    public function getDefaultAction()
    {
        return $this->_defaultAction;
    }
    
    /**
     * Hiba vezérlő nevének lekérdezése
     * 
     * @return string
     */
    public function getErrorController()
    {
        return $this->_errorController;
    }
    
    /**
     * Kivételkezelő vezérlő-akció nevének lekérdezése
     * 
     * @return string
     */
    public function getExceptionAction()
    {
        return $this->_exceptionAction;
    }
    
    /**
     * "Ismeretlen vezérlő hiba" vezérlő-akció nevének lekérdezése
     * 
     * @return string
     */
    public function getNotFoundAction()
    {
        return $this->_notFoundAction;
    }
}
