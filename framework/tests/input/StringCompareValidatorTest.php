<?php

namespace fw\tests\input;

use \fw\input\Validator;
use \fw\input\StringCompareValidator;
use \PHPUnit_Framework_TestCase;

class StringCompareValidatorTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->_validator = StringCompareValidator::build();
    }
    
    /**
     * @dataProvider stringProvider
     */
    public function testAcceptsMatchingStringsOnly($isValid, $value1, $value2)
    {
        $this->assertEquals($isValid, $this->_validator->compareTo($value1)->validate($value2));
    }
    
    /**
     * @dataProvider stringProvider
     */
    public function testReportsLastError($isValid, $value1, $value2)
    {
        $expectedErrors = $isValid ? array() : array(StringCompareValidator::ERROR_NOT_EQUALS => $value1);
        
        $this->_validator->compareTo($value1)->validate($value2);
        
        $this->assertEquals($expectedErrors, $this->_validator->getLastErrors());
    }
    
    public function stringProvider()
    {
        return array(
            array(true,  '', ''),
            array(true,  'a', 'a'),
            array(true,  'Űí', 'Űí'),
            array(false, '', 0),
            array(false, 'a', 'b'),
            array(false, 'a', null),
            array(false, '', false)
        );
    }
}