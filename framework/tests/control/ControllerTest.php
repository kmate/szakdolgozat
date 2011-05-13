<?php

namespace fw\tests\control;

use \fw\config\Configuration;
use \fw\control\Context;
use \fw\control\Controller;
use \PHPUnit_Framework_TestCase;

class ControllerTest extends PHPUnit_Framework_TestCase
{
    private $_contextMock;
    private $_controllerMock;
    
    public function setUp()
    {
        $this->_contextMock    = $this->getMock('\\fw\\control\\Context', array('getFrontController'));
        $this->_controllerMock = $this->getMockForAbstractClass(
            '\\fw\\control\\Controller',
            array($this->_contextMock)
        );
    }
    
    public function tearDown()
    {
        unset($this->_contextMock);
        unset($this->_controllerMock);
    }
    
    public function testHasMagicContextGetter()
    {
        $this->assertEquals($this->_contextMock, $this->_controllerMock->context);
    }
    
    public function testHasMagicGetterToContextProperties()
    {
        $this->_contextMock->expects($this->once())
                           ->method('getFrontController');
        
        $this->assertNull($this->_controllerMock->frontController);
    }
}