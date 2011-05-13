<?php

namespace fw\tests\control;

use \fw\control\HttpContext;
use \fw\control\FrontController;
use \fw\control\RouteInfo;
use \PHPUnit_Framework_TestCase;

class HttpContextTest extends PHPUnit_Framework_TestCase
{
    private $_context;
    
    public function setUp()
    {
        $_GET['getVariable']   = 'getValue';
        $_POST['postVariable'] = 'postValue';
        
        $this->_context = new HttpContext();
        $this->_context->setFrontController(new FrontController());
    }
    
    public function tearDown()
    {
        unset($this->_context);
    }
    
    public function testHasMagicGetParametersGetter()
    {
        $this->assertEquals($_GET, $this->_context->getParameters->toArray());
    }
    
    public function testHasMagicPostParametersGetter()
    {
        $this->assertEquals($_POST, $this->_context->postParameters->toArray());
    }
    
    public function testInheritedMagicMethodsAreAvailable()
    {
        $routeInfo = new RouteInfo('controller', 'action');
        $this->_context->setRouteInfo($routeInfo);
        
        $this->assertEquals($routeInfo, $this->_context->routeInfo);
    }
    
    public function testDefaultViewIsTemplateView()
    {
        $this->assertInstanceOf('\\fw\\view\\TemplateView', $this->_context->view);
    }
    
    public function testViewTemplatesAreSetWhenRouteInfoChanges()
    {
        $routeInfo = new RouteInfo('controller', 'action');
        
        $mockView = $this->getMock('\\fw\\view\\TemplateView');
        $mockView->expects($this->once())
                 ->method('setTemplatesByRouteInfo')
                 ->with($routeInfo);
        
        $this->_context->setView($mockView);
        $this->_context->setRouteInfo($routeInfo);
    }
}