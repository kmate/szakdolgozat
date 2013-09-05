<?php

namespace fw\config;

/**
 * XML-formátumú fájl konfiguráció
 * 
 * @author Karácsony Máté
 */
class XmlConfiguration extends FileBasedConfiguration
{
    const SECTION_ATTRIBUTE_NAME     = 'name';
    const SECTION_ATTRIBUTE_INHERITS = 'inherits';
    
    protected function _parseFile($filePath)
    {
        libxml_use_internal_errors(true);
        libxml_clear_errors();
        
        $schemaPath = join(DIRECTORY_SEPARATOR, array(__DIR__, 'schema', 'configuration.xsd'));
        
        $document = new \DOMDocument();
        $document->preserveWhiteSpace = false;
        
        if (!@$document->load($filePath))
        {
            return false;
        }
        else if (!@$document->schemaValidate($schemaPath))
        {
            throw new XmlException(
                'Validation failed against schema file: \'' . $schemaPath . '\'',
                XmlException::SCHEMA_VALIDATION_FAILED,
                libxml_get_errors()
            );
        }
        else
        {
            return @simplexml_import_dom($document);
        }
    }
    
    protected function _parseIntoArray($parserResult)
    {
        $arrayResult = array();
        
        foreach ($parserResult->children() as $section)
        {
            $this->_processSection($arrayResult, $section);
        }
        
        return $arrayResult;
    }
    
    private function _processSection(array &$arrayResult, \SimpleXMLElement $section)
    {
        $sectionName = trim($section['name']);
        
        if (isset($section['inherits']))
        {
            $parentSectionName = trim($section['inherits']);
            
            $arrayResult[$sectionName] = $this->_processInheritedSection($arrayResult, $parentSectionName, $section);
        }
        else
        {
            $arrayResult[$sectionName] = $this->_transformSectionData($section);
        }
    }
    
    private function _processInheritedSection(array &$arrayResult, $parentSectionName, \SimpleXMLElement $section)
    {
        if (!isset($arrayResult[$parentSectionName]))
        {
            throw new Exception(
                'Missing parent section: \'' . $parentSectionName . '\'',
                Exception::MISSING_PARENT_SECTION
            );
        }
        else
        {
            return array_replace_recursive(
                $arrayResult[$parentSectionName],
                $this->_transformSectionData($section)
            );
        }
    }
    
    private function _transformSectionData(\SimpleXMLElement $element)
    {
        $transformedData = array();
        
        $this->_transformChildElements($transformedData, $element);
        $this->_transformAttributes($transformedData, $element);
        
        return $transformedData;
    }
    
    private function _transformChildElements(array &$transformedData, \SimpleXMLElement $element)
    {
        foreach ($element->children() as $childElement)
        {
            $key = $childElement->getName();
            
            if (0 < count($childElement) ||
                0 < count($childElement->attributes()))
            {
                $value = $this->_transformSectionData($childElement);
            }
            else
            {
                $value = trim($childElement);
            }
            
            $transformedData[$key] = $value;
        }
    }
    
    private function _transformAttributes(array &$transformedData, \SimpleXMLElement $element)
    {
        foreach ($element->attributes() as $key => $value)
        {
            if (0 != strcasecmp(self::SECTION_ATTRIBUTE_NAME, $key) &&
                0 != strcasecmp(self::SECTION_ATTRIBUTE_INHERITS, $key))
            {
                $transformedData[$key] = trim($value);
            }
        }
    }
}