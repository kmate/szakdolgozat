<?php

namespace fw\view;

use \fw\KeyValueStorage;

class Template
{
    const TEMPLATE_EXTENSION = '.phtml';
    
    private $_templateDirectory;
    private $_templatePath;
    private $_variables;
    
    public function __construct($templateName, $templateDirectory)
    {
        $this->_templateDirectory = $templateDirectory;
        $this->_templatePath      = $templateDirectory . DIRECTORY_SEPARATOR . $templateName . self::TEMPLATE_EXTENSION;
        $this->_variables         = new KeyValueStorage();
    }
    
    public function __get($variable)
    {
        return $this->_variables->{$variable};
    }
    
    public function __set($variable, $value)
    {
        $this->_variables->{$variable} = $value;
    }
    
    public function evaluate($returnContents = false)
    {
        if ($returnContents)
        {
            ob_start();
        }
        
        $this->_evaluateContents();
        
        if ($returnContents)
        {
            $output = ob_get_contents();
            ob_end_clean();
            return $output;
        }
    }
    
    private function _evaluateContents()
    {
        if (!is_readable($this->_templatePath))
        {
            throw new TemplateException(
                'Unable to read template file: \'' . $this->_templatePath . '\'',
                TemplateException::MISSING_TEMPLATE_FILE
            );
        }
        
        extract($this->_variables->toArray(0), EXTR_SKIP);
        require $this->_templatePath;
    }
}