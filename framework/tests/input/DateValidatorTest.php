<?php

namespace fw\tests\input;

use \fw\input\Validator;
use \fw\input\DateValidator;
use \PHPUnit_Framework_TestCase;

class DateValidatorTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->_validator = DateValidator::build();
    }
    
    /**
     * @dataProvider dateProvider
     */
    public function testAcceptsMatchingValidDatesOnly($isValid, $value)
    {
        $this->assertEquals($isValid, $this->_validator->validate($value));
    }
    
    public function dateProvider()
    {
            return array(
            array(true,  '2011. 01. 21.'),
            array(true,  '1991. 12. 04.'),
            array(true,  '2000. 02. 29.'),
            array(false, ''),
            array(false, '12/1/21'),
            array(false, '2010-08-03'),
            array(false, '2011. 04. 31.'),
            array(false, '2011. 02. 29.')
        );
    }
    
    public function testOutputDifferentFormat()
    {
        $validDate     = '2011. 04. 23.';
        $formattedDate = '2011-04-23';
        
        $this->_validator->outputFormat('Y-m-d')->validate($validDate);
        
        $this->assertEquals($formattedDate, $validDate);
    }
    
    /**
     * @dataProvider beforeDateProvider
     */
    public function testValidatesBefore($isValid, $date1, $date2)
    {
        $this->assertEquals($isValid, $this->_validator->before($date1)->validate($date2));
    }
    
    /**
     * @dataProvider beforeDateProvider
     */
    public function testReportsLastBeforeError($isValid, $date1, $date2)
    {
        $expectedErrors = $isValid ? array() : array(DateValidator::ERROR_BEFORE => $date1);
        
        $this->_validator->before($date1)->validate($date2);
        
        $this->assertEquals($expectedErrors, $this->_validator->getLastErrors());
    }
    
    /**
     * @dataProvider beforeDateProvider
     */
    public function testValidatesNotBefore($isNotValid, $date1, $date2)
    {
        $this->assertEquals(!$isNotValid, $this->_validator->notBefore($date1)->validate($date2));
    }
    
    /**
     * @dataProvider beforeDateProvider
     */
    public function testReportsLastNotBeforeError($isNotValid, $date1, $date2)
    {
        $expectedErrors = !$isNotValid ? array() : array(DateValidator::ERROR_NOT_BEFORE => $date1);
        
        $this->_validator->notBefore($date1)->validate($date2);
        
        $this->assertEquals($expectedErrors, $this->_validator->getLastErrors());
    }
    
    public function beforeDateProvider()
    {
        return array(
            array(true,  '2011. 01. 21.', '2011. 01. 20.'),
            array(true,  '2011. 01. 01.', '2010. 12. 31.'),
            array(false, '2010. 04. 25.', '2010. 04. 25.'),
            array(false, '2010. 05. 07.', '2011. 05. 06.'),
            array(false, '2001. 03. 11.', '2006. 08. 12.')
        );
    }
    
    /**
     * @dataProvider afterDateProvider
     */
    public function testValidatesAfter($isValid, $date1, $date2)
    {
        $this->assertEquals($isValid, $this->_validator->after($date1)->validate($date2));
    }
    
    /**
     * @dataProvider afterDateProvider
     */
    public function testReportsLastAfterError($isValid, $date1, $date2)
    {
        $expectedErrors = $isValid ? array() : array(DateValidator::ERROR_AFTER => $date1);
        
        $this->_validator->after($date1)->validate($date2);
        
        $this->assertEquals($expectedErrors, $this->_validator->getLastErrors());
    }
    
    /**
     * @dataProvider afterDateProvider
     */
    public function testValidatesNotAfter($isNotValid, $date1, $date2)
    {
        $this->assertEquals(!$isNotValid, $this->_validator->notAfter($date1)->validate($date2));
    }
    
    /**
     * @dataProvider afterDateProvider
     */
    public function testReportsLastNotAfterError($isNotValid, $date1, $date2)
    {
        $expectedErrors = !$isNotValid ? array() : array(DateValidator::ERROR_NOT_AFTER => $date1);
        
        $this->_validator->notAfter($date1)->validate($date2);
        
        $this->assertEquals($expectedErrors, $this->_validator->getLastErrors());
    }
    
    public function afterDateProvider()
    {
        return array(
            array(true,  '2011. 01. 20.', '2011. 01. 21.'),
            array(true,  '2010. 12. 31.', '2011. 01. 01.'),
            array(false, '2010. 04. 25.', '2010. 04. 25.'),
            array(false, '2011. 05. 06.', '2010. 05. 07.'),
            array(false, '2006. 08. 12.', '2001. 03. 11.')
        );
    }
}