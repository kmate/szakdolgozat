<?php

namespace fw\input;

/**
 * Diszkrét értékhalmaz ellenőrző és szűrő
 * 
 * @author Karácsony Máté
 */
class EnumValidator extends Validator
{
    const ERROR_INVALID_VALUE = 'EnumValidator::ERROR_INVALID_VALUE';
    
    private $_acceptedValues = array();
    
    /**
     * Elfogadott értékhalmaz bővítése
     * 
     * @param  string         további elfogadott érték
     * @return EnumValidator  önmagát adja vissza (láncolt híváshoz)
     */
    public function acceptValue($value)
    {
        if (!in_array($value, $this->_acceptedValues))
        {
            $this->_acceptedValues[] = (string)$value;
        }
        
        return $this;
    }
    
    /**
     * Ellenőrzés és szűrés végrehajtása
     * 
     * @param  mixed  a bemenetről kapott érték
     * @return bool   az ellenőrzés kimenete
     */
    public function validate(&$value)
    {
        $this->_lastErrors = array();
        
        if (!is_string($value))
        {
            $this->_lastErrors[self::ERROR_INVALID_VALUE] = $value;
            
            return false;
        }
        
        if ($this->_sanitize)
        {
            $value = filter_var($value, FILTER_SANITIZE_STRING, FILTER_FLAG_ENCODE_LOW);
        }
        
        $isValid = true;
        $length  = mb_strlen($value);
        
        if ($this->_required && 0 == $length)
        {
            $this->_lastErrors[Validator::ERROR_REQUIRED] = true;
            
            $isValid = false;
        }
        
        if (0 < $length && !in_array($value, $this->_acceptedValues))
        {
            $this->_lastErrors[self::ERROR_INVALID_VALUE] = $value;
            
            $isValid = false;
        }
        
        return $isValid;
    }
}