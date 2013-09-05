<?php

namespace fw\input;

/**
 * Bemenet-ellenőrzők és szűrők ősosztálya
 * 
 * @author Karácsony Máté
 */
abstract class Validator
{
    const ERROR_REQUIRED = 'Validator::ERROR_REQUIRED';
    
    /**
     * Bemenet-ellenőrzőt épít
     * 
     * @return Validator
     */
    public static function build()
    {
        return new static();
    }
    
    protected $_required   = false;
    protected $_sanitize   = true;
    protected $_lastErrors = array();
    
    protected function __construct() {}
    
    /**
     * Annak beállítása, hogy üres értékeket érvénytelennek tekintünk-e
     * 
     * @param  bool       igaz érték esetén érvénytelennek tekintjük az üres értékeket
     * @return Validátor  önmagát adja vissza (láncolt híváshoz)
     */
    public function required($required = true)
    {
        $this->_required = $required;
        
        return $this;
    }
    
    /**
     * Szűrés be- vagy kikapcsolása
     * 
     * @param  bool       igaz érték esetén bekapcsoljuk a szűrést
     * @return Validátor  önmagát adja vissza (láncolt híváshoz)
     */
    public function sanitize($sanitize = true)
    {
        $this->_sanitize = $sanitize;
        
        return $this;
    }
    
    /**
     * Legutóbbi validálási hibák lekérdezése
     * 
     * @return mixed
     */
    public function getLastErrors()
    {
        return $this->_lastErrors;
    }
    
    /**
     * Ellenőrzés és szűrés végrehajtása
     * 
     * @param  mixed  a bemenetről kapott érték
     * @return bool   az ellenőrzés kimenete
     */
    abstract public function validate(&$value);
}