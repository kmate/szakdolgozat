<?php

namespace fw\config;

/**
 * XML-konfiguráció kivétel
 * 
 * @author Karácsony Máté
 */
class XmlException extends Exception
{
    const SCHEMA_VALIDATION_FAILED = 1;
    
    private $_validationErrors;
    
    public function __construct($message = null, $code = 0, $validationErrors = null, Exception $previous = null)
    {
        $this->_validationErrors = $validationErrors;
        
        parent::__construct($message, $code, $previous);
    }
    
    /**
     * XML-ellenőrzési hibák lekérése
     * 
     * @return array validálási hibák
     */
    public function getValidationErrors()
    {
        return $this->_validationErrors;
    }
}
