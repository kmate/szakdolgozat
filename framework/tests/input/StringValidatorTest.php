<?php

namespace fw\tests\input;

use \fw\input\Validator;
use \fw\input\StringValidator;
use \PHPUnit_Framework_TestCase;

class StringValidatorTest extends PHPUnit_Framework_TestCase
{
    protected $_validator;
    
    public function setUp()
    {
        $this->_validator = StringValidator::build();
    }
    
    public function tearDown()
    {
        unset($this->_validator);
    }
    
    /**
     * @dataProvider stringsDataProvider
     */
    public function testAcceptsStringsOnly($isValid, $value)
    {
        $this->assertEquals($isValid, $this->_validator->validate($value));
    }
    
    public function stringsDataProvider()
    {
        return array(
            array(true,  'test'),
            array(true,  '42'),
            array(true,  ''),
            array(false, 0),
            array(false, 42.0),
            array(false, null),
            array(false, false),
            array(false, true),
            array(false, array()),
            array(false, new \stdClass())
        );
    }
    
    public function testValidatesRequired()
    {
        $this->_validator->required();
        
        $value = '';
        
        $this->assertEquals(false, $this->_validator->validate($value));
        $this->assertEquals(array(Validator::ERROR_REQUIRED => true), $this->_validator->getLastErrors());
    }
    
    /**
     * @dataProvider minLengthDataProvider
     */
    public function testValidatesMinLength($isValid, $minLength, $value)
    {
        $this->assertEquals($isValid, $this->_validator->minLength($minLength)->validate($value));
    }
    
    /**
     * @dataProvider minLengthDataProvider
     */
    public function testReportsLastMinLengthError($isValid, $minLength, $value)
    {
        $expectedErrors = $isValid ? array() : array(StringValidator::ERROR_MIN_LENGTH => $minLength);
        
        $this->_validator->minLength($minLength)->validate($value);
        
        $this->assertEquals($expectedErrors, $this->_validator->getLastErrors());
    }
    
    public function minLengthDataProvider()
    {
        return array(
            array(true,   1, '1'),
            array(true,   0, ''),
            array(false, 10, '123456789'),
            array(false,  1, ''),
            array(true,  10, '0123456789'),
            array(true,  10, '01234567890123456789'),
        );
    }
    
    /**
     * @dataProvider maxLengthDataProvider
     */
    public function testValidatesMaxLength($isValid, $maxLength, $value)
    {
        $this->assertEquals($isValid, $this->_validator->maxLength($maxLength)->validate($value));
    }
    
    /**
     * @dataProvider maxLengthDataProvider
     */
    public function testReportsLastMaxLengthError($isValid, $maxLength, $value)
    {
        $expectedErrors = $isValid ? array() : array(StringValidator::ERROR_MAX_LENGTH => $maxLength);
        
        $this->_validator->maxLength($maxLength)->validate($value);
        
        $this->assertEquals($expectedErrors, $this->_validator->getLastErrors());
    }
    
    public function maxLengthDataProvider()
    {
        return array(
            array(true,   1, '1'),
            array(true,   0, '0123456789'),
            array(false, 10, '01234567891'),
            array(false,  1, '12'),
            array(true,  10, '0123456789'),
            array(true,  10, '0'),
        );
    }
    
    /**
     * @dataProvider sanitizeDataProvider
     */
    public function testSanitizesStrings($inputValue, $sanitizedValue)
    {
        $this->_validator->validate($inputValue);
        
        $this->assertEquals($sanitizedValue, $inputValue);
    }
    
    /**
     * @dataProvider sanitizeDataProvider
     */
    public function testSanitizeCanBeDisabled($inputValue, $sanitizedValue)
    {
        $this->_validator->sanitize(false);
        $this->_validator->validate($inputValue);
        
        $this->assertNotEquals($sanitizedValue, $inputValue);
    }
    
    public function sanitizeDataProvider()
    {
        return array(
            array(chr(10), '&#10;'),
            array('a<script>b', 'ab'),
            array('<scri pt>', ''),
            array('<scr' . chr(0) . 'ipt>', ''),
            array('<scr' . chr(255) . 'ipt>', '')
        );
    }
}