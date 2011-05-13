<?php

namespace fw\tests\input;

use \ReflectionClass;
use \fw\KeyValueStorage;
use \fw\input\ValidatorGroup;
use \PHPUnit_Framework_TestCase;

class ValidatorTest extends PHPUnit_Framework_TestCase
{
    public function testBuildMethodUsesLateStaticBinding()
    {
        $validatorMock = $this->getMockForAbstractClass('\\fw\\input\\Validator', array(), '', false);
        $mockClassName = get_class($validatorMock);
        
        $this->assertInstanceof($mockClassName, $mockClassName::build());
    }
    
    public function testConstructorIsProtected()
    {
        $classInfo   = new ReflectionClass('\\fw\\input\\Validator');
        $constructor = $classInfo->getConstructor();
        
        $this->assertNotNull($constructor);
        $this->assertTrue($constructor->isProtected());
    }
}
