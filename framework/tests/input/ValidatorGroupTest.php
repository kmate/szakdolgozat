<?php

namespace fw\tests\input;

use \fw\KeyValueStorage;
use \fw\input\Validator;
use \fw\input\ValidatorGroup;
use \PHPUnit_Framework_TestCase;

class ValidatorGroupTest extends PHPUnit_Framework_TestCase
{
    private $_parameters;
    
    public function setUp()
    {
        $this->_parameters = new KeyValueStorage(array(
            'key1' => 'value1',
            'key2' => 'value2',
            'key3' => 'value3'
        ));
    }
    
    public function tearDown()
    {
        unset($this->_parameters);
    }
    
    public function testValidationValueIsPassedByReference()
    {
        $validatorGroup = $this->getMock('\\fw\input\\ValidatorGroup', array('validate'), array(), '', false);
        $validatorGroup->expects($this->once())
                       ->method('validate')
                       ->will($this->returnCallback(function (&$value) { $value = array(); }));
        
        $validatorGroup->validate($this->_parameters);
        
        $this->assertEmpty($this->_parameters);
    }
    
    public function testConvertsBackToKeyValueStorage()
    {
        ValidatorGroup::build()->validate($this->_parameters);
        
        $this->assertInstanceOf('\\fw\\KeyValueStorage', $this->_parameters);
    }
    
    public function testValidateInvokesAllSubvalidators()
    {
        ValidatorGroup::build()
            ->addValidator($this->_getValidatorMock('value1'), 'key1')
            ->addValidator($this->_getValidatorMock('value2'), 'key2')
            ->validate($this->_parameters);
    }
    
    public function testMultipleValidatorsCanValidateTheSameKey()
    {
        ValidatorGroup::build()
            ->addValidator($this->_getValidatorMock('value1'), 'key1')
            ->addValidator($this->_getValidatorMock('value1'), 'key1')
            ->validate($this->_parameters);
    }
    
    public function testValidationSucceedsIfAllValidatorSucceeds()
    {
        $result = ValidatorGroup::build()
                    ->addValidator($this->_getValidatorStub(true), 'key1')
                    ->addValidator($this->_getValidatorStub(true), 'key2')
                    ->validate($this->_parameters);
        
        $this->assertTrue($result);
    }
    
    public function testValidationFailsIfOneValidatorFails()
    {
        $result = ValidatorGroup::build()
                    ->addValidator($this->_getValidatorStub(true),  'key1')
                    ->addValidator($this->_getValidatorStub(false), 'key2')
                    ->addValidator($this->_getValidatorStub(true),  'key3')
                    ->validate($this->_parameters);
        
        $this->assertFalse($result);
    }
    
    public function testSupportsDefaultValues()
    {
        ValidatorGroup::build()
            ->addValidator($this->_getValidatorMock(0),  'key4',  0)
            ->addValidator($this->_getValidatorMock(''), 'key5', '')
            ->validate($this->_parameters);
    }
    
    public function testWritesBackDefaultValues()
    {
        ValidatorGroup::build()
            ->addValidator($this->_getValidatorMock('default4'), 'key4', 'default4')
            ->addValidator($this->_getValidatorMock('default5'), 'key5', 'default5')
            ->validate($this->_parameters);
        
        $this->assertEquals('default4', $this->_parameters->key4);
        $this->assertEquals('default5', $this->_parameters->key5);
    }
    
    public function testWritesBackDefaultValuesToArray()
    {
        $arrayParameters = $this->_parameters->toArray();
        
        ValidatorGroup::build()
            ->addValidator($this->_getValidatorMock('default4'), 'key4', 'default4')
            ->addValidator($this->_getValidatorMock('default5'), 'key5', 'default5')
            ->validate($arrayParameters);
        
        $this->assertEquals('default4', $arrayParameters['key4']);
        $this->assertEquals('default5', $arrayParameters['key5']);
    }
    
    public function testCollectsLastErrorsByKey()
    {
        $key2ErrorsA = array('error1' => 'detail1');
        $key2ErrorsB = array('error2' => 'detail2', 'error3' => 'detail3');
        
        $expectedErrors = array(
            'key2' => array_merge($key2ErrorsA, $key2ErrorsB)
        );
        
        $validatorGroup = ValidatorGroup::build()
                            ->addValidator($this->_getValidatorStub(true), 'key1')
                            ->addValidator($this->_getValidatorErrorStub($key2ErrorsA), 'key2')
                            ->addValidator($this->_getValidatorErrorStub($key2ErrorsB), 'key2');
        
        $validatorGroup->validate($this->_parameters);
        
        $this->assertEquals(new KeyValueStorage($expectedErrors), $validatorGroup->getLastErrors());
    }
    
    private function _getValidatorMock($validateCallParameter)
    {
        $validatorMock = $this->getMockForAbstractClass('\\fw\\input\\Validator', array(), '', false);
        $validatorMock->expects($this->once())
                      ->method('validate')
                      ->with($validateCallParameter);
        
        return $validatorMock;
    }
    
    private function _getValidatorStub($validateReturnValue)
    {
        $validatorMock = $this->getMockForAbstractClass('\\fw\\input\\Validator', array(), '', false);
        $validatorMock->expects($this->once())
                      ->method('validate')
                      ->will($this->returnValue($validateReturnValue));
        
        return $validatorMock;
    }
    
    private function _getValidatorErrorStub(array $lastErrors = array())
    {
        $validatorMock = $this->getMock(
            '\\fw\\input\\Validator',
            array('validate', 'getLastErrors'),
            array(),
            '',
            false
        );
        $validatorMock->expects($this->once())
                      ->method('validate')
                      ->will($this->returnValue(false));
        $validatorMock->expects($this->once())
                      ->method('getLastErrors')
                      ->will($this->returnValue($lastErrors));
        
        return $validatorMock;
    }
}
