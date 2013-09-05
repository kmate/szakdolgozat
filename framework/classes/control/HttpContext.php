<?php

namespace fw\control;

use \fw\KeyValueStorage;
use \fw\view\TemplateView;

/**
 * Vezérlési környezet HTTP protokollhoz
 * 
 * @author Karácsony Máté
 */
class HttpContext extends Context
{
    protected $_getParameters;
    protected $_postParameters;
    
    public function __construct()
    {
        $this->_getParameters  = new KeyValueStorage($_GET);
        $this->_postParameters = new KeyValueStorage($_POST);
    }
    
    /**
     * GET-paraméterek lekérdezése
     * 
     * @return KeyValueStorage
     */
    public function getGetParameters()
    {
        return $this->_getParameters;
    }
    
    /**
     * POST-paraméterek lekérdezése
     * 
     * @return KeyValueStorage
     */
    public function getPostParameters()
    {
        return $this->_postParameters;
    }
    
    /**
     * Útvonal információ beállítása
     * 
     * @param  RouteInfo
     * @return void
     */
    public function setRouteInfo(RouteInfo $routeInfo)
    {
        $this->getView()->setTemplatesByRouteInfo($routeInfo);
        
        parent::setRouteInfo($routeInfo);
    }
    
    /**
     * Nézet lekérdezése
     * 
     * @return View
     */
    public function getView()
    {
        if (null == $this->_view)
        {
            $this->_view = new TemplateView($this->getConfiguration());
        }
        
        return parent::getView();
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
            case 'getParameters':
                return $this->getGetParameters();
            
            case 'postParameters':
                return $this->getPostParameters();
            
            default:
                return parent::__get($propertyName);
        }
    }
}