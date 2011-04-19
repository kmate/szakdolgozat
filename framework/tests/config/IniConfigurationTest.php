<?php

namespace fw\tests\config;

use \fw\config\Configuration;
use \fw\config\IniConfiguration;
use \PHPUnit_Framework_TestCase;

if (!defined('CONFIG_TEST_ASSETS_PATH'))
{
    define('CONFIG_TEST_ASSETS_PATH', TEST_ASSETS_PATH . DIRECTORY_SEPARATOR . 'config');
}

define(
    'INCORRECT_INI_CONFIG_PATH',
    join(DIRECTORY_SEPARATOR, array(CONFIG_TEST_ASSETS_PATH, 'Incorrect.ini'))
);
define(
    'INVALID_SECION_NAME_INI_CONFIG_PATH',
    join(DIRECTORY_SEPARATOR, array(CONFIG_TEST_ASSETS_PATH, 'InvalidSectionName.ini'))
);
define(
    'MISSING_PARENT_SECTION_INI_CONFIG_PATH',
    join(DIRECTORY_SEPARATOR, array(CONFIG_TEST_ASSETS_PATH, 'MissingParentSection.ini'))
);
define(
    'VALID_INI_CONFIG_PATH',
    join(DIRECTORY_SEPARATOR, array(CONFIG_TEST_ASSETS_PATH, 'Valid.ini'))
);

class IniConfigurationTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        Configuration::setActiveSection(Configuration::DEFAULT_SECTION);
    }
    
    /**
     * @expectedException     \fw\config\Exception
     * @expectedExceptionCode 3 (Exception::UNABLE_TO_PARSE_FILE)
     */
    public function testConstructorThrowsExceptionOnIncorrectIniFile()
    {
        $iniConfiguration = new IniConfiguration(INCORRECT_INI_CONFIG_PATH);
    }
    
    /**
     * @expectedException     \fw\config\Exception
     * @expectedExceptionCode 1 (Exception::INVALID_SECTION_NAME)
     */
    public function testConstructorThrowsExceptionOnInvalidSectionName()
    {
        $iniConfiguration = new IniConfiguration(INVALID_SECION_NAME_INI_CONFIG_PATH);
    }
    
    /**
     * @expectedException     \fw\config\Exception
     * @expectedExceptionCode 4 (Exception::MISSING_PARENT_SECTION)
     */
    public function testConstructorThrowsExceptionOnMissingParentSection()
    {
        $iniConfiguration = new IniConfiguration(MISSING_PARENT_SECTION_INI_CONFIG_PATH);
    }
    
    public function testCanParseValidIniFile()
    {
        $iniConfiguration = new IniConfiguration(VALID_INI_CONFIG_PATH);
        
        Configuration::setActiveSection('test');
        
        $this->assertEquals('value1', $iniConfiguration->key1);
        $this->assertFalse($iniConfiguration->has('key2'));
    }
    
    public function testSectionInheritance()
    {
        $iniConfiguration = new IniConfiguration(VALID_INI_CONFIG_PATH);
        
        Configuration::setActiveSection('production');
        
        $this->assertEquals('value1',  $iniConfiguration->key1);
        $this->assertEquals('value2b', $iniConfiguration->key2);
        $this->assertEquals('value3',  $iniConfiguration->key3);
        $this->assertEquals('value6',  $iniConfiguration->key4->key5->key6);
    }
}
