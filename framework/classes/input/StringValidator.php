<?php

namespace fw\input;

class StringValidator extends Validator
{
    const ERROR_MIN_LENGTH = 'StringValidator::ERROR_MIN_LENGTH';
    const ERROR_MAX_LENGTH = 'StringValidator::ERROR_MAX_LENGTH';
    
    private $_minLength = 0;
    private $_maxLength = 0;
    
    public function minLength($minLength = 0)
    {
        $this->_minLength = $minLength;
        
        return $this;
    }
    
    public function maxLength($maxLength = 0)
    {
        $this->_maxLength = $maxLength;
        
        return $this;
    }
    
    public function validate(&$value)
    {
        $this->_lastErrors = array();
        
        if (!is_string($value))
        {
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
        
        if ($length < $this->_minLength)
        {
            $this->_lastErrors[self::ERROR_MIN_LENGTH] = $this->_minLength;
            
            $isValid = false;
        }
        
        if (0 < $this->_maxLength && $length > $this->_maxLength)
        {
            $this->_lastErrors[self::ERROR_MAX_LENGTH] = $this->_maxLength;
            
            $isValid = false;
        }
        
        return $isValid;
    }
}