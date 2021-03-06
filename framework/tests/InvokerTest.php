<?php

namespace fw\tests;

use \fw\ClassLoader;
use \fw\Invoker;
use \fw\InvokerException;
use \PHPUnit_Framework_TestCase;

class InvokerTest extends PHPUnit_Framework_TestCase
{
    private static $_classLoader;
    
    public static function setUpBeforeClass()
    {
        self::$_classLoader = new ClassLoader(TEST_ASSETS_PATH, '', true);
    }
    
    public static function tearDownAfterClass()
    {
        self::$_classLoader->unregister();
        self::$_classLoader = null;
    }
    
    /**
     * @expectedException        \fw\InvokerException
     * @expectedExceptionCode    1 (InvokerException::MISSING_CLASS)
     */
    public function testThrowsExceptionOnNonexistentClass()
    {
        $invoker = new Invoker('\\NonexistentClass');
    }
    
    /**
     * @expectedException        \fw\InvokerException
     * @expectedExceptionCode    2 (InvokerException::INVALID_IMPLEMENTATION)
     */
    public function testThrowsExceptionOnInvalidInterface()
    {
        $invoker = new Invoker('\\EClass');
        $invoker->checkInterface('\\TestInterface');
    }
    
    /**
     * @expectedException        \fw\InvokerException
     * @expectedExceptionCode    3 (InvokerException::INVALID_SUBCLASS)
     */
    public function testThrowsExceptionOnInvalidParentClass()
    {
        $invoker = new Invoker('\\EClass');
        $invoker->checkParentClass('\\TestParentClass');
    }
    
    public function testValidCheckDoesNotThrowException()
    {
        $invoker = new Invoker('\\TestClass');
        $invoker->checkInterface('\\TestInterface');
        $invoker->checkParentClass('\\TestParentClass');
    }
    
    /**
     * @expectedException        \fw\InvokerException
     * @expectedExceptionCode    4 (InvokerException::MISSING_METHOD)
     */
    public function testInvokeOnNonexistentMethodThrowsException()
    {
        $invoker = new Invoker('\\TestClass');
        $invoker->invoke('nonexistentMethod');
    }
    
    public function testInvokeReturnsValue()
    {
        $invoker = new Invoker('\\TestClass');
        
        $this->assertTrue($invoker->invoke('echoMethod', array()));
    }
    
    /**
     * @dataProvider argumentsProvider
     */
    public function testArgumentMatching($methodName, $matching, $arguments, $finalArguments = null)
    {
        $invoker = new Invoker('\\TestClass');
        $method  = $invoker->getMethod($methodName);
        
        if (!$matching)
        {
            $this->setExpectedException('\\fw\\InvokerException', null, InvokerException::INVALID_PARAMETERS);
            $invoker->matchArguments($arguments, $method);
        }
        else
        {
            $this->assertEquals($finalArguments ?: $arguments, $invoker->matchArguments($arguments, $method));
        }
    }
    
    public function argumentsProvider()
    {
        return array(
            array('zeroParameters',     true,  array()),
            array('zeroParameters',     false, array(1)),
            array('alphabetParameters', true,  array('c' => 3, 'a' => 1, 'b' => 2), array(1, 2, 3)),
            array('alphabetParameters', false, array('d' => 3, 'a' => 1, 'e' => 2)),
            array('optionalParameter',  true,  array(1)),
            array('arrayParameter',     false, array('test')),
            array('typedParameters',    false, array(new \stdClass(), 'b')),
            array('typedParameters',    true,  array(new \stdClass(), new \stdClass())),
            array('typedNullParameter', true,  array())
        );
    }
}
