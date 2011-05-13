<?php

namespace fw\input;

class DateValidator extends RegexpValidator
{
    const ERROR_INVALID_DATE = 'DateValidator::ERROR_INVALID_DATE';
    const ERROR_BEFORE       = 'DateValidator::ERROR_BEFORE';
    const ERROR_NOT_BEFORE   = 'DateValidator::ERROR_NOT_BEFORE';
    const ERROR_AFTER        = 'DateValidator::ERROR_AFTER';
    const ERROR_NOT_AFTER    = 'DateValidator::ERROR_NOT_AFTER';
    
    const DEFAULT_PATTERN = '/^(\d{4})\. (\d{2})\. (\d{2})\.$/';
    const DEFAULT_FORMAT  = 'Y. m. d.';
    
    private $_inputFormat;
    private $_outputFormat;
    
    private $_beforeStamp    = null;
    private $_notBeforeStamp = null;
    
    private $_afterStamp    = null;
    private $_notAfterStamp = null;
    
    protected function __construct()
    {
        $this->pattern(self::DEFAULT_PATTERN);
        $this->inputFormat(self::DEFAULT_FORMAT);
        $this->outputFormat(self::DEFAULT_FORMAT);
    }
    
    public function inputFormat($inputFormat)
    {
        $this->_inputFormat = $inputFormat;
        
        return $this;
    }
    
    public function outputFormat($outputFormat)
    {
        $this->_outputFormat = $outputFormat;
        
        return $this;
    }
    
    public function before($before)
    {
        $beforeStamp = $this->_parseDate($before);
        
        $this->_beforeStamp = false !== $beforeStamp ? $beforeStamp : null;
        
        return $this;
    }
    
    public function notBefore($notBefore)
    {
        $notBeforeStamp = $this->_parseDate($notBefore);
        
        $this->_notBeforeStamp = false !== $notBeforeStamp ? $notBeforeStamp : null;
        
        return $this;
    }
    
    public function after($after)
    {
        $afterStamp = $this->_parseDate($after);
        
        $this->_afterStamp = false !== $afterStamp ? $afterStamp : null;
        
        return $this;
    }
    
    public function notAfter($notAfter)
    {
        $notAfterStamp = $this->_parseDate($notAfter);
        
        $this->_notAfterStamp = false !== $notAfterStamp ? $notAfterStamp : null;
        
        return $this;
    }
    
    public function validate(&$value)
    {
        if (!($isValid = parent::validate($value)))
        {
            return false;
        }
        
        $valueStamp = $this->_parseDate($value);
        
        if (false === $valueStamp)
        {
            $this->_lastErrors[self::ERROR_INVALID_DATE] = $value;
            
            return false;
        }
        
        if (null !== $this->_beforeStamp && !($valueStamp < $this->_beforeStamp))
        {
            $this->_lastErrors[self::ERROR_BEFORE] = $this->_formatDate($this->_beforeStamp, $this->_inputFormat);
            
            $isValid = false;
        }
        
        if (null !== $this->_notBeforeStamp && !($valueStamp >= $this->_notBeforeStamp))
        {
            $this->_lastErrors[self::ERROR_NOT_BEFORE] = $this->_formatDate($this->_notBeforeStamp, $this->_inputFormat);
            
            $isValid = false;
        }
        
        if (null !== $this->_afterStamp && !($valueStamp > $this->_afterStamp))
        {
            $this->_lastErrors[self::ERROR_AFTER] = $this->_formatDate($this->_afterStamp, $this->_inputFormat);
            
            $isValid = false;
        }
        
        if (null !== $this->_notAfterStamp && !($valueStamp <= $this->_notAfterStamp))
        {
            $this->_lastErrors[self::ERROR_NOT_AFTER] = $this->_formatDate($this->_notAfterStamp, $this->_inputFormat);
            
            $isValid = false;
        }
        
        $value = $this->_formatDate($valueStamp, $this->_outputFormat);
        
        return $isValid;
    }
    
    private function _parseDate($value)
    {
        $dateInfo = date_parse_from_format($this->_inputFormat, $value);
        
        if (is_array($dateInfo) && checkdate($dateInfo['month'], $dateInfo['day'], $dateInfo['year']))
        {
            return mktime(0, 0, 0, $dateInfo['month'], $dateInfo['day'], $dateInfo['year']);
        }
        else
        {
            return false;
        }
    }
    
    private function _formatDate($value, $format)
    {
        return date($format, $value);
    }
}