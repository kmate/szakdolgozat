<?php

namespace fw\tests\control;

use \fw\config\Configuration;
use \fw\control\FrontController;
use \fw\control\RouteInfo;
use \PHPUnit_Extensions_OutputTestCase;

if (!defined('CONTROL_TEST_ASSETS_PATH'))
{
    define('CONTROL_TEST_ASSETS_PATH', TEST_ASSETS_PATH . DIRECTORY_SEPARATOR . 'control');
}

class FrontControllerTest extends PHPUnit_Extensions_OutputTestCase
{
    private $_frontController;
    
    public function setUp()
    {
        $configuration = new Configuration(array(
            'controller' => array(
                'classpath' => CONTROL_TEST_ASSETS_PATH,
            )
        ));
        
        $this->_frontController = new FrontController($configuration);
    }
    
    public function tearDown()
    {
        unset($this->_frontController);
    }
    
    private function _dipatchWithRoute($route)
    {
        $this->_frontController->dispatch($route);
        
        return $this->_frontController->getRouter()->parseRoute($route);
    }
    
    public function testGetControllerClassName()
    {
        $routeInfo = new RouteInfo('test', 'index');
        
        $this->assertEquals('\TestController', $this->_frontController->getControllerClassName($routeInfo));
    }
    
    public function testGetControllerClassNameWithHypens()
    {
        $routeInfo = new RouteInfo('test-with-hypens', 'index');
        
        $this->assertEquals('\TestWithHypensController', $this->_frontController->getControllerClassName($routeInfo));
    }
    
    public function testGetActionMethodName()
    {
        $routeInfo = new RouteInfo('test', 'index');
        
        $this->assertEquals('indexAction', $this->_frontController->getActionMethodName($routeInfo));
    }
    
    public function testGetActionMethodNameWithHypens()
    {
        $routeInfo = new RouteInfo('test', 'index-with-hypens');
        
        $this->assertEquals('indexWithHypensAction', $this->_frontController->getActionMethodName($routeInfo));
    }
    
    public function testRouterInstanceCanBeChanged()
    {
        $routerMock = $this->getMockForAbstractClass('\\fw\\control\\Router');
        
        $this->_frontController->setRouter($routerMock);
        
        $this->assertEquals($routerMock, $this->_frontController->getRouter());
    }
    
    public function testSetContextSetsFrontController()
    {
        $contextMock = $this->getMock('\\fw\\control\\Context', array('setFrontController'));
        $contextMock->expects($this->once())
                    ->method('setFrontController')
                    ->with($this->_frontController);
        
        $this->_frontController->setContext($contextMock);
    }
    
    public function testConfigurationIsInjectedIntoRouter()
    {
        $configuration = new Configuration(array(
            'controller' => array(
                'classpath' => CONTROL_TEST_ASSETS_PATH,
                'namespace' => FrontController::DEFAULT_NAMESPACE
            ),
            'router' => array(
                'default_controller' => 'config-default-controller',
                'default_action'     => 'config-default-action'
            )
        ));
        
        $this->_frontController = new FrontController($configuration);
        
        $route             = '';
        $parsedRouteInfo   = $this->_frontController->getRouter()->parseRoute($route);
        $expectedRouteInfo = new RouteInfo('config-default-controller', 'config-default-action');
        
        $this->assertEquals($expectedRouteInfo, $parsedRouteInfo);
    }
    
    public function testDispatchWithRouteInfo()
    {
        $route     = 'test/route-info-export';
        $routeInfo = $this->_dipatchWithRoute($route);
        
        $this->expectOutputString(var_export($routeInfo, true));
    }
    
    public function testDispatchWithMissingControllerForwardsToNotFound()
    {
        $route = 'missing';
        $this->_dipatchWithRoute($route);
        
        $this->expectOutputString('notFoundAction');
    }
    
    /**
     * @expectedException     \fw\control\Exception
     * @expectedExceptionCode 1 (Exception::MISSING_CONTROLLER_CLASS)
     */
    public function testDispatchWithMissingControllerThrowsException()
    {
        $configuration = new Configuration(array(
            'controller' => array(
                'classpath' => CONTROL_TEST_ASSETS_PATH,
            ),
            'router' => array(
                'error_controller' => 'missing',
            )
        ));
        
        $this->_frontController = new FrontController($configuration);
        
        $route = 'missing';
        $this->_dipatchWithRoute($route);
    }
    
    public function testDispatchWithMissingActionForwardsToNotFound()
    {
        $route = 'test/missing';
        $this->_dipatchWithRoute($route);
        
        $this->expectOutputString('notFoundAction');
    }
    
    /**
     * @expectedException     \fw\control\Exception
     * @expectedExceptionCode 3 (Exception::MISSING_ACTION_METHOD)
     */
    public function testDispatchWithMissingActionThrowsException()
    {
        $configuration = new Configuration(array(
            'controller' => array(
                'classpath' => CONTROL_TEST_ASSETS_PATH,
            ),
            'router' => array(
                'not_found_action' => 'missing',
            )
        ));
        
        $this->_frontController = new FrontController($configuration);
        
        $route = 'test/missing';
        $this->_dipatchWithRoute($route);
    }
    
    /**
     * @expectedException     \fw\control\Exception
     * @expectedExceptionCode 2 (Exception::INVALID_CONTROLLER_CLASS)
     */
    public function testDispatchWithInvalidControllerClassThrowsException()
    {
        $route = 'invalid';
        $this->_dipatchWithRoute($route);
    }
    
    public function testDispatchForwardsToExceptionAction()
    {
        $route = 'test/exception';
        $this->_dipatchWithRoute($route);
        
        $this->expectOutputString($this->_frontController->getLastException());
    }
    
    public function testForwardWithRouteInfo()
    {
        $route = 'test/forward-to-route-info-export';
        $this->_dipatchWithRoute($route);
        
        $this->expectOutputString(var_export(new RouteInfo('test', 'routeInfoExport'), true));
    }
    
    public function testForwardToDefaultWithRouteInfo()
    {
        $configuration = new Configuration(array(
            'controller' => array(
                'classpath' => CONTROL_TEST_ASSETS_PATH,
            ),
            'router' => array(
                'default_controller' => 'test',
                'default_action'     => 'routeInfoExport',
            )
        ));
        
        $this->_frontController = new FrontController($configuration);
        
        $route = 'test/forward-to-default';
        $this->_dipatchWithRoute($route);
        
        $this->expectOutputString(var_export(new RouteInfo('test', 'routeInfoExport'), true));
    }
    
    public function testForwardToMissingActionForwardsToNotFound()
    {
        $route = 'test/forward-to-missing';
        $this->_dipatchWithRoute($route);
        
        $this->expectOutputString('notFoundAction');
    }
    
    public function testExternalForwardToDefaultCallsRedirect()
    {
        $router = $this->getMockForAbstractClass('\\fw\\control\\Router');
        $router->expects($this->any())
               ->method('parseRoute')
               ->will($this->returnValue(new RouteInfo('test', 'forward-to-default-external')));
        $router->expects($this->once())
               ->method('redirect');
        
        $this->_frontController->setRouter($router);
        
        $route = 'test/forward-to-default-external';
        $this->_dipatchWithRoute($route);
    }
    
    public function testDispatchWithControllerFromNamespace()
    {
        $configuration = new Configuration(array(
            'controller' => array(
                'classpath' => CONTROL_TEST_ASSETS_PATH,
                'namespace' => 'control'
            )
        ));
        
        $this->_frontController = new FrontController($configuration);
        
        $route     = 'namespace-test/namespace-echo';
        $routeInfo = $this->_dipatchWithRoute($route);
        
        $this->expectOutputString('control');
    }
}