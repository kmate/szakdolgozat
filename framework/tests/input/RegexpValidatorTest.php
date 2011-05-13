<?php

namespace fw\tests\input;

use \fw\input\Validator;
use \fw\input\RegexpValidator;
use \PHPUnit_Framework_TestCase;

class RegexpValidatorTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->_validator = RegexpValidator::build();
    }
    
    /**
     * @dataProvider patternProvider
     */
    public function testAcceptsMatchingStringsOnly($isValid, $pattern, $value)
    {
        $this->assertEquals($isValid, $this->_validator->pattern($pattern)->validate($value));
    }
    
    /**
     * @dataProvider patternProvider
     */
    public function testReportsLastError($isValid, $pattern, $value)
    {
        $expectedErrors = $isValid ? array() : array(RegexpValidator::ERROR_NO_MATCH => $pattern);
        
        $this->_validator->pattern($pattern)->validate($value);
        
        $this->assertEquals($expectedErrors, $this->_validator->getLastErrors());
    }
    
    public function patternProvider()
    {
        return array(
            array(true,  '', 'anything'),
            array(true,  '/^[a-z]+$/', 'abcd'),
            array(true,  '/\pL/u', 'Ű'),
            array(true,  '/^ab*/', 'abbbbb'),
            array(false, '/^ab*/', 'bbbbb'),
            array(false, '/^[a-z]{3,5}/', 'az'),
            array(false, '/^[0-9]?$/', ' ')
        );
    }
}