<?php

namespace fw\tests\input;

use \fw\input\Validator;
use \fw\input\EmailValidator;
use \PHPUnit_Framework_TestCase;

class EmailValidatorTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->_validator = EmailValidator::build();
    }
    
    /**
     * @dataProvider emailProvider
     */
    public function testAcceptsEmailAddressesOnly($isValid, $value)
    {
        $this->assertEquals($isValid, $this->_validator->validate($value));
    }
    
    /**
     * @dataProvider emailProvider
     */
    public function testReportsLastError($isValid, $value)
    {
        $expectedErrors = $isValid ? array() : array(EmailValidator::ERROR_INVALID_ADDRESS => true);
        
        $this->_validator->validate($value);
        
        $this->assertEquals($expectedErrors, $this->_validator->getLastErrors());
    }
    
    public function emailProvider()
    {
        return array(
            array(true,  'test@test.hu'),
            array(true,  'a.b.c@sub.domain.info'),
            array(true,  'a-b.c@d-e.f-g.co.uk'),
            array(false, '@invalid'),
            array(false, 'no@tld'),
            array(false, 'a?b@e:z.hu')
        );
    }
}