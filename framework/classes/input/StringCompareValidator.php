<?php

namespace fw\input;

class StringCompareValidator extends Validator
{
    const ERROR_NOT_EQUALS = 'StringCompareValidator::ERROR_NOT_EQUALS';
    
    private $_compareTo = '';
    
    public function compareTo($compareTo = '')
    {
        $this->_compareTo = (string)$compareTo;
        
        return $this;
    }
    
    public function validate(&$value)
    {
        $this->_lastErrors = array();
        
        if (!is_string($value))
        {
            $this->_lastErrors[self::ERROR_NOT_EQUALS] = $this->_compareTo;
            
            return false;
        }
        
        if ($this->_sanitize)
        {
            $value = filter_var($value, FILTER_SANITIZE_STRING, FILTER_FLAG_ENCODE_LOW);
        }
        
        $isValid = true;
        
        if (0 !== strcmp($this->_compareTo, $value))
        {
            $this->_lastErrors[self::ERROR_NOT_EQUALS] = $this->_compareTo;
            
            $isValid = false;
        }
        
        return $isValid;
    }
}