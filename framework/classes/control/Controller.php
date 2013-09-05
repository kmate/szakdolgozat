<?php

namespace fw\control;

/**
 * Vezérlők ősosztálya
 * 
 * @author Karácsony Máté
 */
abstract class Controller
{
    protected $_context;
    
    /**
     * Új vezérlőt hoz létre a megadott környezettel
     * 
     * @param Context  környezet
     */
    public function __construct(Context $context)
    {
        $this->_context = $context;
    }
    
    /**
     * Környezet adattagjainak lekérdezése (alternatív szintaxis)
     * 
     * @param  string  környezet adattag neve
     * @return mixed
     */
    public function __get($propertyName)
    {
        if ('context' === $propertyName)
        {
            return $this->_context;
        }
        else
        {
            return $this->_context->{$propertyName};
        }
    }
}