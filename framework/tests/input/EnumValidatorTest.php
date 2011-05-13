<?php

namespace fw\tests\input;

use \fw\input\Validator;
use \fw\input\EnumValidator;
use \PHPUnit_Framework_TestCase;

class EnumValidatorTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->_validator = EnumValidator::build();
        $this->_validator->acceptValue('a')
                         ->acceptValue('b')
                         ->acceptValue('c');
    }
    
    /**
     * @dataProvider valueProvider
     */
    public function testAcceptsEnumValuesOnly($isValid, $value)
    {
        $this->assertEquals($isValid, $this->_validator->validate($value));
    }
    
    /**
     * @dataProvider valueProvider
     */
    public function testReportsLastError($isValid, $value)
    {
        $expectedErrors = $isValid ? array() : array(EnumValidator::ERROR_INVALID_VALUE => $value);
        
        $this->_validator->validate($value);
        
        $this->assertEquals($expectedErrors, $this->_validator->getLastErrors());
    }
    
    public function valueProvider()
    {
        return array(
            array(true,  ''),
            array(true,  'a'),
            array(true,  'b'),
            array(true,  'c'),
            array(false, 'd'),
            array(false, true),
            array(false, false),
            array(false, null),
            array(false, 0)
        );
    }
    
    public function testValidatesRequired()
    {
        $this->_validator->required();
        
        $value = '';
        
        $this->assertEquals(false, $this->_validator->validate($value));
        $this->assertEquals(array(Validator::ERROR_REQUIRED => true), $this->_validator->getLastErrors());
    }
}