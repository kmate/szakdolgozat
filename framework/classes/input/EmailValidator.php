<?php

namespace fw\input;

/**
 * E-mail cím ellenőrző és szűrő
 * 
 * @author Karácsony Máté
 */
class EmailValidator extends StringValidator
{
    const ERROR_INVALID_ADDRESS = 'EmailValidator::ERROR_INVALID_ADDRESS';
    
    /**
     * Ellenőrzés és szűrés végrehajtása
     * 
     * @param  mixed  a bemenetről kapott érték
     * @return bool   az ellenőrzés kimenete
     */
    public function validate(&$value)
    {
        $isValid = parent::validate($value);
        
        if (!filter_var($value, FILTER_VALIDATE_EMAIL))
        {
            $this->_lastErrors[self::ERROR_INVALID_ADDRESS] = true;
            
            $isValid = false;
        }
        
        return $isValid;
    }
}