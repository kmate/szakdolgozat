<?php

namespace fw\tests\control;

use \fw\config\Configuration;
use \fw\control\FrontController;
use \fw\control\RouteInfo;
use \PHPUnit_Framework_TestCase;

class ContextTest extends PHPUnit_Framework_TestCase
{
    private $_contextMock;
    
    public function setUp()
    {
        $this->_contextMock = $this->getMockForAbstractClass('\\fw\\control\\Context');
    }
    
    public function tearDown()
    {
        unset($this->_contextMock);
    }
    
    public function testHasMagicRouteInfoGetter()
    {
        $routeInfo = new RouteInfo('controller', 'action');
        $this->_contextMock->setRouteInfo($routeInfo);
        
        $this->assertEquals($routeInfo, $this->_contextMock->routeInfo);
    }
    
    public function testHasMagicRouteParametersGetter()
    {
        $routeParameters = array('p1' => 'v1', 'p2' => array('v21', 'v22'));
        $routeInfo       = new RouteInfo('controller', 'action', $routeParameters);
        $this->_contextMock->setRouteInfo($routeInfo);
        
        $this->assertEquals($routeParameters, $this->_contextMock->routeParameters->toArray());
    }
    
    public function testHasMagicFrontControllerGetter()
    {
        $frontController = new FrontController();
        $this->_contextMock->setFrontController($frontController);
        
        $this->assertEquals($frontController, $this->_contextMock->frontController);
    }
    
    public function testHasMagicConfigurationGetter()
    {
        $configuration   = new Configuration();
        $frontController = new FrontController($configuration);
        $this->_contextMock->setFrontController($frontController);
        
        $this->assertEquals($configuration, $this->_contextMock->configuration);
    }
    
    public function testHasMagicViewGetter()
    {
        $viewMock = $this->getMockForAbstractClass('\\fw\view\View');
        $this->_contextMock->setView($viewMock);
        
        $this->assertInstanceOf('\\fw\view\View', $this->_contextMock->view);
    }
    
    public function testMagicGetterReturnsNullForUndefinedProperty()
    {
        $this->assertNull($this->_contextMock->undefinedProperty);
    }
}