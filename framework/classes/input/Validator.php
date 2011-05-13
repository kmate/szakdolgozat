<?php

namespace fw\input;

abstract class Validator
{
    const ERROR_REQUIRED = 'Validator::ERROR_REQUIRED';
    
    public static function build()
    {
        return new static();
    }
    
    protected $_required   = false;
    protected $_sanitize   = true;
    protected $_lastErrors = array();
    
    protected function __construct() {}
    
    public function required($required = true)
    {
        $this->_required = $required;
        
        return $this;
    }
    
    public function sanitize($sanitize = true)
    {
        $this->_sanitize = $sanitize;
        
        return $this;
    }
    
    public function getLastErrors()
    {
        return $this->_lastErrors;
    }
    
    abstract public function validate(&$value);
}