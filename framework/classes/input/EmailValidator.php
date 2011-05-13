<?php

namespace fw\input;

class EmailValidator extends StringValidator
{
    const ERROR_INVALID_ADDRESS = 'EmailValidator::ERROR_INVALID_ADDRESS';
    
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