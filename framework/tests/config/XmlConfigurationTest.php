<?php

namespace fw\tests\config;

use \fw\config\Configuration;
use \fw\config\XmlConfiguration;
use \fw\config\XmlException;
use \PHPUnit_Framework_TestCase;

if (!defined('CONFIG_TEST_ASSETS_PATH'))
{
    define('CONFIG_TEST_ASSETS_PATH', TEST_ASSETS_PATH . DIRECTORY_SEPARATOR . 'config');
}

define(
    'INCORRECT_XML_CONFIG_PATH',
    join(DIRECTORY_SEPARATOR, array(CONFIG_TEST_ASSETS_PATH, 'Incorrect.xml'))
);
define(
    'INVALID_XML_CONFIG_PATH',
    join(DIRECTORY_SEPARATOR, array(CONFIG_TEST_ASSETS_PATH, 'Invalid.xml'))
);
define(
    'MISSING_PARENT_SECTION_XML_CONFIG_PATH',
    join(DIRECTORY_SEPARATOR, array(CONFIG_TEST_ASSETS_PATH, 'MissingParentSection.xml'))
);
define(
    'VALID_XML_CONFIG_PATH',
    join(DIRECTORY_SEPARATOR, array(CONFIG_TEST_ASSETS_PATH, 'Valid.xml'))
);

class XmlConfigurationTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        Configuration::setActiveSection(Configuration::DEFAULT_SECTION);
    }
    
    /**
     * @expectedException     \fw\config\Exception
     * @expectedExceptionCode 3 (Exception::UNABLE_TO_PARSE_FILE)
     */
    public function testConstructorThrowsExceptionOnIncorrectXmlFile()
    {
        $xmlConfiguration = new XmlConfiguration(INCORRECT_XML_CONFIG_PATH);
    }
    
    public function testConstructorThrowsExceptionOnInvalidXmlFile()
    {
        try
        {
            $xmlConfiguration = new XmlConfiguration(INVALID_XML_CONFIG_PATH);
        }
        catch (XmlException $e)
        {
            if (XmlException::SCHEMA_VALIDATION_FAILED == $e->getCode() &&
                1 == count($e->getValidationErrors()))
            {
                return;
            }
        }
        
        $this->fail('Testing XmlException parameters failed.');
    }
    
    /**
     * @expectedException     \fw\config\Exception
     * @expectedExceptionCode 4 (Exception::MISSING_PARENT_SECTION)
     */
    public function testConstructorThrowsExceptionOnMissingParentSection()
    {
        $xmlConfiguration = new XmlConfiguration(MISSING_PARENT_SECTION_XML_CONFIG_PATH);
    }
    
    public function testCanParseValidXmlFile()
    {
        $xmlConfiguration = new XmlConfiguration(VALID_XML_CONFIG_PATH);
        
        Configuration::setActiveSection('production');
        
        Configuration::setActiveSection('test');
        
        $this->assertEquals('value1', $xmlConfiguration->key1);
        $this->assertFalse($xmlConfiguration->has('key2'));
    }
    
    public function testSectionInheritance()
    {
        $xmlConfiguration = new XmlConfiguration(VALID_XML_CONFIG_PATH);
        
        Configuration::setActiveSection('production');
        
        $this->assertEquals('value1',  $xmlConfiguration->key1);
        $this->assertEquals('value2b', $xmlConfiguration->key2);
        $this->assertEquals('value3',  $xmlConfiguration->key3);
        $this->assertEquals('value6',  $xmlConfiguration->key4->key5->key6);
        $this->assertEquals('value7',  $xmlConfiguration->key4->key7);
    }
}
