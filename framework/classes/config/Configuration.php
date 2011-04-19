<?php

namespace fw\config;

use \fw\KeyValueStorage;

class Configuration extends KeyValueStorage
{
    const DEFAULT_SECTION = 'default';
    
    private static $_activeSection = self::DEFAULT_SECTION;
    
    public static function getActiveSection()
    {
        return self::$_activeSection;
    }
    
    public static function setActiveSection($newActiveSection)
    {
        $newActiveSection = trim($newActiveSection);
        $oldActiveSection = self::$_activeSection;
        
        if (self::isValidSectionName($newActiveSection))
        {
            self::$_activeSection = $newActiveSection;
        }
        else
        {
            self::_throwInvalidSectionNameException($newActiveSection);
        }
        
        return $oldActiveSection;
    }
    
    public static function isValidSectionName($sectionName)
    {
        return 1 === preg_match('/^[a-z][_a-z0-9]+$/i', $sectionName);
    }
    
    protected static function _throwInvalidSectionNameException($sectionName)
    {
        throw new Exception(
            'Invalid section name: \'' . $sectionName . '\'',
            Exception::INVALID_SECTION_NAME
        );
    }
    
    public function __construct(array $data = array(), $withSections = false)
    {
        parent::__construct(
            $withSections
                ? $data
                : array(self::$_activeSection => $data)
        );
    }
    
    public function has($key)
    {
        $this->_ensureActiveSectionKeyExists();
        
        return $this->_data[self::$_activeSection]->has($key);
    }
    
    public function get($key, $defaultValue = null)
    {
        $this->_ensureActiveSectionKeyExists();
        
        return $this->_data[self::$_activeSection]->get($key, $defaultValue);
    }
    
    public function set($key, $value)
    {
        $this->_ensureActiveSectionKeyExists();
        
        $this->_data[self::$_activeSection]->set($key, $value);
        
        return $this;
    }
    
    private function _ensureActiveSectionKeyExists()
    {
        if (!isset($this->_data[self::$_activeSection])
        || !($this->_data[self::$_activeSection] instanceof KeyValueStorage))
        {
            $this->_data[self::$_activeSection] = new KeyValueStorage();
        }
    }
    
    public function merge(Configuration $other)
    {
        $mergedArray     = array_replace_recursive($this->toArray(), $other->toArray());
        $mergedStructure = new KeyValueStorage($mergedArray);
        
        $mergedConfiguration        = new Configuration();
        $mergedConfiguration->_data = $mergedStructure->_data;
        
        return $mergedConfiguration;
    }
}