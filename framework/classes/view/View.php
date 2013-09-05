<?php

namespace fw\view;

use fw\control\RouteInfo;

/**
 * Nézetek ősosztálya
 * 
 * @author Karácsony Máté
 */
abstract class View
{
    protected $_rendered   = false;
    protected $_autoRender = true;
    
    /**
     * Automatikus nézet-kiértékelés be- vagy kikapcsolása
     *
     * @return bool  engedélyezett-e a az automatikus nézet-kiértékelés
     * @return void
     */
    public function setAutoRender($value)
    {
        $this->_autoRender = $value;
    }
    
    /**
     * Automatikus nézet-kiértékelés futtatása (ha engedélyezett és még nem történt meg)
     *
     * @return void
     */
    public function runAutoRenderer()
    {
        if ($this->_autoRender && !$this->_rendered)
        {
            $this->render();
        }
    }
    
    /**
     * Nézet kiértékelése
     * 
     * @param  bool  visszatérés a kiértékelés eredményével (hamis érték esetén a kimenetre írja)
     * @return string
     */
    public function render($returnsOutput = false)
    {
        $this->_rendered = true;
        
        return $this->_render($returnsOutput);
    }
    
    /**
     * Linket generál egy vezérlő megadott akciójához, a kivánt paraméterekkel
     *
     * @param  string  vezérlő neve
     * @param  string  vezérlő-akció neve
     * @param  array   paraméterek
     * @return string  a generált link
     */
    public function generateLink($controllerName, $actionName, array $parameters = array())
    {
        return ViewUtils::generateLink($controllerName, $actionName, $parameters);
    }
    
    /**
     * Lekéri az alkalmazás relatív hivatkozásainak viszonyítási pontjául szolgáló URL-t
     *
     * @return string
     */
    public function getBaseHref()
    {
        return ViewUtils::getBaseHref();
    }
    
    /**
     * Nézet kiértékelése
     * 
     * @param  bool  visszatérés a kiértékelés eredményével (hamis érték esetén a kimenetre írja)
     * @return string
     */
    abstract protected function _render($returnsOutput);
}