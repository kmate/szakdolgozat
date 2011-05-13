<?php

namespace fw\tests\control\hooks;

use \fw\SessionManager;
use \fw\config\Configuration;
use \fw\control\Context;
use \fw\control\FrontController;
use \fw\control\RouteInfo;
use \fw\control\hooks\AuthenticationHook;
use \PHPUnit_Extensions_OutputTestCase;

if (!defined('CONTROL_TEST_ASSETS_PATH'))
{
    define('CONTROL_TEST_ASSETS_PATH', TEST_ASSETS_PATH . DIRECTORY_SEPARATOR . 'control');
}

class AuthenticationHookTest extends PHPUnit_Extensions_OutputTestCase
{
    private $_hookMock;
    
    public function setUp()
    {
        $this->_hookMock = $this->getMockForAbstractClass('\\fw\\control\\hooks\\AuthenticationHook');
    }
    
    public function tearDown()
    {
        unset($this->_hookMock);
        
        SessionManager::destroy();
    }
    
    private function _createContextMock(Configuration $config = null, $route = '')
    {
        if (null == $config)
        {
            $config = new Configuration();
        }
        
        $frontControllerMock = $this->getMock('\\fw\\control\\FrontController', array('forward'), array($config));
        
        $contextMock = $this->getMockForAbstractClass('\\fw\\control\\Context');
        $contextMock->setFrontController($frontControllerMock);
        $contextMock->setRouteInfo($frontControllerMock->getRouter()->parseRoute($route));
        
        return $contextMock;
    }
    
    public function testStartsSessionOnExecute()
    {
        $context = $this->_createContextMock();
        
        $this->_hookMock
             ->expects($this->once())
             ->method('validateSession')
             ->will($this->returnValue(true));
        
        $this->_hookMock->execute($context);
        
        $this->assertNotEmpty(session_id());
    }
    
    public function testStartsSessionOnExecuteWithDefaultName()
    {
        $context = $this->_createContextMock();
        
        $this->_hookMock
             ->expects($this->once())
             ->method('validateSession')
             ->will($this->returnValue(true));
        
        $this->_hookMock->execute($context);
        
        $this->assertEquals(ini_get('session.name'), session_name());
    }
    
    public function testStartsSessionOnExecuteWithConfiguredName()
    {
        $context = $this->_createContextMock(new Configuration(array(
            'auth' => array(
                'session_name' => 'testSession'
            )
        )));
        
        $this->_hookMock
             ->expects($this->once())
             ->method('validateSession')
             ->will($this->returnValue(true));
        
        $this->_hookMock->execute($context);
        
        $this->assertEquals('testSession', session_name());
    }
    
    public function testInvokesValidate()
    {
        $context = $this->_createContextMock();
        
        $this->_hookMock
             ->expects($this->once())
             ->method('validateSession');
         
        $this->_hookMock->execute($context);
    }
    
    public function testDoesNotInvokesValidateWhenRouteFilteredOut()
    {
        $context = $this->_createContextMock(new Configuration(array(
            'auth' => array(
                'public' => array(
                    'public-controller' => array(
                        'public-action' => true
                    )
                )
            )
        )), 'public-controller/public-action');
        
        $this->_hookMock
             ->expects($this->never())
             ->method('validateSession');
        
        $this->_hookMock->execute($context);
    }
    
    public function testForwardsWhenValidationFail()
    {
        $loginControllerName = 'login-controller';
        $loginActionName     = 'login-action';
        
        $context = $this->_createContextMock(new Configuration(array(
            'auth' => array(
                'login_controller' => $loginControllerName,
                'login_action'     => $loginActionName
            )
        )));
        
        $context->frontController
                ->expects($this->once())
                ->method('forward')
                ->with($this->equalTo(new RouteInfo($loginControllerName, $loginActionName)));
        
        $this->_hookMock
             ->expects($this->once())
             ->method('validateSession')
             ->will($this->returnValue(false));
        
        $this->_hookMock->execute($context);
    }
}