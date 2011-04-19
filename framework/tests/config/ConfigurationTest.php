<?php

namespace fw\tests\config;

use \fw\config\Configuration;
use \PHPUnit_Framework_TestCase;

class ConfigurationTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        Configuration::setActiveSection(Configuration::DEFAULT_SECTION);
    }
    
    public function testActiveSectionCanBeChangedAndTrimmed()
    {
        Configuration::setActiveSection('  production ');
        
        $this->assertEquals('production', Configuration::getActiveSection());
    }
    
    /**
     * @dataProvider invalidSectionNamesProvider
     * 
     * @expectedException     \fw\config\Exception
     * @expectedExceptionCode 1 (Exception::INVALID_SECTION_NAME)
     */
    public function testActiveSectionCantBeChangedToInvalidName($sectionName)
    {
        Configuration::setActiveSection($sectionName);
    }
    
    public function invalidSectionNamesProvider()
    {
        return array(
            array('invalid section name'),
            array(42),
            array(false)
        );
    }
    
    public function testDifferentSectionDataAreIndependent()
    {
        $oldActiveSection = Configuration::setActiveSection('production');
        
        $configuration = new Configuration();
        
        $configuration->key1 = true;
        
        Configuration::setActiveSection($oldActiveSection);
        
        $this->assertFalse(isset($configuration->key1));
    }
    
    public function testMerge()
    {
        $c1 = new Configuration(array(
            'key1' => 'value1',
            'key2' => 'value2a'
        ));
        
        $c2 = new Configuration(array(
            'key2' => 'value2b',
            'key3' => 'value3'
        ));
        
        $c3 = $c1->merge($c2);
        
        $this->assertEquals(
            array(
                Configuration::DEFAULT_SECTION => array(
                    'key1' => 'value1',
                    'key2' => 'value2b',
                    'key3' => 'value3'
                )
            ),
            $c3->toArray()
        );
    }
}
