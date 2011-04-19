<?php

namespace fw\config;

class IniConfiguration extends FileBasedConfiguration
{
    private $_keySeparator;
    
    public function __construct($filePath, $keySeparator = '.')
    {
        $this->_keySeparator = $keySeparator;
        
        parent::__construct($filePath);
    }
    
    protected function _parseFile($filePath)
    {
        return @parse_ini_file($filePath, true);
    }
    
    protected function _parseIntoArray($parserResult)
    {
        $arrayResult = array();
        
        foreach ($parserResult as $sectionName => $sectionData)
        {
            $this->_processSection($arrayResult, $sectionName, $sectionData);
        }
        
        return $arrayResult;
    }
    
    private function _processSection(array &$arrayResult, $sectionName, array $sectionData)
    {
        $matchResult = preg_match(
            '/^\s*([a-z][_a-z0-9]+)(?:\s*\:\s*([a-z][_a-z0-9]+))?\s*$/i',
            $sectionName,
            $matches
        );
        
        if (1 === $matchResult)
        {
            if (3 == count($matches))
            {
                $this->_processInheritedSection(
                    $arrayResult,
                    $matches[1],
                    $matches[2],
                    $sectionData
                );
            }
            else
            {
                $arrayResult[$matches[1]] = $this->_transformSectionData($sectionData);
            }
        }
        else
        {
            Configuration::_throwInvalidSectionNameException($sectionName);
        }
    }
    
    private function _processInheritedSection(array &$arrayResult, $currentSection, $parentSection, array $sectionData)
    {
        if (!isset($arrayResult[$parentSection]))
        {
            throw new Exception(
                'Missing parent section: \'' . $parentSection . '\'',
                Exception::MISSING_PARENT_SECTION
            );
        }
        else
        {
            $arrayResult[$currentSection] = array_replace_recursive(
                $arrayResult[$parentSection],
                $this->_transformSectionData($sectionData)
            );
        }
    }
    
    private function _transformSectionData(array $sectionData)
    {
        $transformedData = array();
        
        foreach ($sectionData as $key => $value)
        {
            $keyParts = explode($this->_keySeparator, $key);
            
            if (1 < count($keyParts))
            {
                $key = array_shift($keyParts);
                
                while (0 < count($keyParts))
                {
                    $currentKey = array_pop($keyParts);
                    $value      = array($currentKey => $value);
                }
            }
            
            $transformedData[$key] = $value;
        }
        
        return $transformedData;
    }
}