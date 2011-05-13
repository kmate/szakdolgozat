<?php

namespace fw\tests\control;

use \fw\config\Configuration;
use \fw\control\Context;
use \fw\control\FrontController;
use \fw\control\RouteInfo;
use \PHPUnit_Extensions_OutputTestCase;

if (!defined('CONTROL_TEST_ASSETS_PATH'))
{
    define('CONTROL_TEST_ASSETS_PATH', TEST_ASSETS_PATH . DIRECTORY_SEPARATOR . 'control');
}

class FrontControllerHooksTest extends PHPUnit_Extensions_OutputTestCase
{
    private $_frontController;
    
    private function _dispatchWithRouteAndHooks($route, array $preHooks = array(), array $postHooks = array())
    {
        $configuration = new Configuration(array(
            'controller' => array(
                'classpath'  => CONTROL_TEST_ASSETS_PATH,
                'pre_hooks'  => $preHooks,
                'post_hooks' => $postHooks
            ),
        ));
        
        $this->_frontController = new FrontController($configuration);
        $this->_frontController->dispatch($route);
        
        return $this->_frontController->getRouter()->parseRoute($route);
    }
    
    public function tearDown()
    {
        unset($this->_frontController);
    }
    
    public function testPreHooksInvoked()
    {
        $preHooks  = array('RouteInfoHook');
        $route     = 'test/empty';
        $routeInfo = $this->_dispatchWithRouteAndHooks($route, $preHooks);
        
        $this->expectOutputString(var_export($routeInfo, true));
    }
    
    public function testPostHooksInvoked()
    {
        $postHooks = array('RouteInfoHook');
        $route     = 'test/empty';
        $routeInfo = $this->_dispatchWithRouteAndHooks($route, array(), $postHooks);
        
        $this->expectOutputString(var_export($routeInfo, true));
    }
    
    public function testForwardWorksInPreHook()
    {
        $preHooks  = array('ForwardHook');
        $route     = 'test/empty';
        $routeInfo = new RouteInfo('test', 'route-info-export');
        
        $this->_dispatchWithRouteAndHooks($route, $preHooks);
        
        $this->expectOutputString(var_export($routeInfo, true));
    }
    
    /**
     * @expectedException     \fw\control\Exception
     * @expectedExceptionCode 2 (Exception::INVALID_CONTROLLER_CLASS)
     */
    public function testForwardToExceptionRethrowsExceptionInPreHook()
    {
        $preHooks  = array('ForwardToInvalidHook');
        $route     = 'test/empty';
        $routeInfo = new RouteInfo('test', 'route-info-export');
        
        $this->_dispatchWithRouteAndHooks($route, $preHooks);
    }
    
    /**
     * @expectedException     \fw\control\Exception
     * @expectedExceptionCode 4 (Exception::MISSING_HOOK_CLASS)
     */
    public function testMissingPreHookThrowsException()
    {
        $preHooks  = array('MissingHook');
        $route     = 'test/empty';
        $routeInfo = $this->_dispatchWithRouteAndHooks($route, $preHooks);
    }
    
    /**
     * @expectedException     \fw\control\Exception
     * @expectedExceptionCode 5 (Exception::INVALID_HOOK_CLASS)
     */
    public function testInvalidPreHookThrowsException()
    {
        $preHooks  = array('InvalidHook');
        $route     = 'test/empty';
        $routeInfo = $this->_dispatchWithRouteAndHooks($route, $preHooks);
    }
}