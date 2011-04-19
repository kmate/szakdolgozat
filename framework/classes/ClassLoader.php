<?php

namespace fw;

class ClassLoader
{
    private $_classPath;
    private $_defaultNamespace;
    
    private $_registered;
    
    public function __construct(
        $classPath = __DIR__,
        $defaultNamespace = '',
        $autoRegister = false)
    {
        $this->_classPath        = $classPath;
        $this->_defaultNamespace = (0 == strcmp('\\', $defaultNamespace) ? '' : $defaultNamespace);
        
        if ($autoRegister)
        {
            $this->register();
        }
    }
    
    public function register()
    {
        if (!$this->_registered)
        {
            $this->_registered = true;
            
            spl_autoload_register(array($this, 'autoload'));
        }
    }
    
    public function unregister()
    {
        if ($this->_registered)
        {
            $this->_registered = false;
            
            spl_autoload_unregister(array($this, 'autoload'));
        }
    }
    
    public function autoload($fullQualifiedClassName)
    {
        if (!$this->classShouldBeLoaded($fullQualifiedClassName))
        {
            return;
        }
        
        $filePath = $this->getPath($fullQualifiedClassName);
        
        if (file_exists($filePath))
        {
            require_once $filePath;
        }
        else
        {
            require_once __DIR__ . DIRECTORY_SEPARATOR . 'ClassLoaderException.php';
            throw new ClassLoaderException('Unable to load class \'' . $fullQualifiedClassName . '\'');
        }
    }
    
    private function classShouldBeLoaded($fullQualifiedClassName)
    {
        return 0 == strlen($this->_defaultNamespace) || 0 === strpos($fullQualifiedClassName, $this->_defaultNamespace);
    }
    
    private function getPath($fullQualifiedClassName)
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
