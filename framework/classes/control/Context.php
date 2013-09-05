<?php

namespace fw\control;

use \fw\KeyValueStorage;
use \fw\view\View;

/**
 * Vezérlési környezet
 * 
 * @author Karácsony Máté
 */
abstract class Context
{
    protected $_frontController;
    protected $_routeInfo;
    protected $_view;
    
    /**
     * Útvonal információ lekérdezése
     * 
     * @return RouteInfo
     */
    public function getRouteInfo()
    {
        return $this->_routeInfo;
    }
    
    /**
     * Útvonal információ beállítása
     * 
     * @param  RouteInfo
     * @return void
     */
    public function setRouteInfo(RouteInfo $routeInfo)
    {
        $this->_routeInfo = $routeInfo;
    }
    
    /**
     * Útvonal paraméterek lekérdezése
     * 
     * @return KeyValueStorage
     */
    public function getRouteParameters()
    {
        return $this->_routeInfo->getParameters();
    }
    
    /**
     * Fő vezérlő lekérdezése
     * 
     * @return FrontController
     */
    public function getFrontController()
    {
        return $this->_frontController;
    }
    
    /**
     * Fő vezérlő beállítása
     * 
     * @param  FrontController
     * @return void
     */
    public function setFrontController(FrontController $frontController)
    {
        $this->_frontController = $frontController;
    }
    
    /**
     * Konfiguráció lekérdezése
     * 
     * @return Configuration
     */
    public function getConfiguration()
    {
        return $this->_frontController->getConfiguration();
    }
    
    /**
     * Nézet lekérdezése
     * 
     * @return View
     */
    public function getView()
    {
        return $this->_view;
    }
    
    /**
     * Nézet beállítása
     * 
     * @param  View
     * @return void
     */
    public function setView(View $view)
    {
        $this->_view = $view;
    }
    
    /**
     * Adattagok lekérdezése (alternatív szintaxis)
     * 
     * @param  string  adattag neve
     * @return mixed
     */
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