<?php

namespace fw\input;

class RegexpValidator extends StringValidator
{
    const ERROR_NO_MATCH = 'RegexpValidator::ERROR_NO_MATCH';
    
    private $_options = null;
    
    public function pattern($pattern = '')
    {
        if (is_string($pattern) && !empty($pattern))
        {
            $this->_options = array(
                'options' => array(
                    'regexp' => $pattern
                )
            );
        }
        else
        {
            $this->_options = null;
        }
        
        return $this;
    }
    
    public function validate(&$value)
    {
        $isValid = parent::validate($value);
        
        if (null != $this->_options && !filter_var($value, FILTER_VALIDATE_REGEXP, $this->_options))
        {
            $this->_lastErrors[self::ERROR_NO_MATCH] = $this->_options['options']['regexp'];
            
            $isValid = false;
        }
        
        return $isValid;
    }
}