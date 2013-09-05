<?php

namespace fw;

/**
 * Osztálybetöltő
 * 
 * @author Karácsony Máté
 */
class ClassLoader
{
    private $_classPath;
    private $_defaultNamespace;
    
    private $_registered;
    
    /**
     * Létrehoz egy osztálybetöltő példányt,
     * és opcionálisan be is regisztrálja a betöltők láncába
     * 
     * @param string  a betöltendő osztályok fő névterének könyvtára
     * @param string  a betöltendő osztályok fő névtere
     * @param bool    automatikus regisztráció az osztálybetöltők láncába
     */
    public function __construct(
        $classPath = __DIR__,
        $defaultNamespace = '',
        $autoRegister = false)
    {
        $this->_classPath        = $classPath;
        $this->_defaultNamespace = '\\' === $defaultNamespace ? '' : $defaultNamespace;
        
        if ($autoRegister)
        {
            $this->register();
        }
    }
    
    /**
     * Betöltő regisztrálása
     * 
     * @return void
     */
    public function register()
    {
        if (!$this->_registered)
        {
            $this->_registered = true;
            
            spl_autoload_register(array($this, 'autoload'));
        }
    }
    
    /**
     * Betöltő regisztrációjának megszűntetése
     * (hatástalanítja a betöltőt)
     * 
     * @return void
     */
    public function unregister()
    {
        if ($this->_registered)
        {
            $this->_registered = false;
            
            spl_autoload_unregister(array($this, 'autoload'));
        }
    }
    
    /**
     * Elvégzi paraméterként megadott osztály betöltését,
     * ha fő névtere egyezik a konstruktorban megadottal
     *
     * @param  string  a betöltendő osztály teljesen minősített neve
     * @return void
     */
    public function autoload($fullQualifiedClassName)
    {
        if (!$this->_classShouldBeLoaded($fullQualifiedClassName))
        {
            return;
        }
        
        $filePath = $this->_getPath($fullQualifiedClassName);
        
        if (is_readable($filePath))
        {
            require_once $filePath;
        }
    }
    
    private function _classShouldBeLoaded($fullQualifiedClassName)
    {
        return 0 == strlen($this->_defaultNamespace) || 0 === strpos($fullQualifiedClassName, $this->_defaultNamespace);
    }
    
    private function _getPath($fullQualifiedClassName)
    {
        $filePath = realpath($this->_classPath);
        $fileName = $fullQualifiedClassName;
        
        if (strlen($this->_defaultNamespace) > 0)
        {
            $index = strpos($fullQualifiedClassName, $this->_defaultNamespace);
            
            if (0 === $index)
            {
                $fileName = substr($fullQualifiedClassName, strlen($this->_defaultNamespace));
            }
        }
        else
        {
            $filePath .= DIRECTORY_SEPARATOR;
        }
        
        $fileName .= '.php';
        $filePath .= $fileName;
        $filePath  = str_replace('\\', DIRECTORY_SEPARATOR, $filePath);
        
        return $filePath;
    }
}
