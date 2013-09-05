<?php

namespace fw\view;

use \fw\KeyValueStorage;

/**
 * Fájl alapú sablon
 * 
 * @author Karácsony Máté
 */
class Template
{
    const TEMPLATE_EXTENSION = '.phtml';
    
    private $_templateDirectory;
    private $_templatePath;
    private $_variables;
    
    /**
     * Új sablont hoz létre a neve és könyvtára alapján
     * 
     * @param string  a sablon neve
     * @param string  a sablon könyvtára
     */
    public function __construct($templateName, $templateDirectory)
    {
        $this->_templateDirectory = $templateDirectory;
        $this->_templatePath      = $templateDirectory . DIRECTORY_SEPARATOR . $templateName . self::TEMPLATE_EXTENSION;
        $this->_variables         = new KeyValueStorage();
    }
    
    /**
     * Sablon-változó lekérdezése
     * 
     * @param  string  a változó neve
     * @return mixed   a változó értéke
     */
    public function __get($variable)
    {
        return $this->_variables->{$variable};
    }
    
    /**
     * Sablon-változó beállítása
     * 
     * @param  string  a változó neve
     * @param  mixed   a változó értéke
     * @return void
     */
    public function __set($variable, $value)
    {
        $this->_variables->{$variable} = $value;
    }
    
    /**
     * Sablon kiértékelése
     * 
     * @param  bool  visszatérés a kiértékelés eredményével (hamis érték esetén a kimenetre írja)
     * @return string
     */
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