<?php

namespace fw\tests\control;

use \fw\config\Configuration;
use \fw\control\RouteInfo;
use \PHPUnit_Framework_TestCase;

class RouterTest extends PHPUnit_Framework_TestCase
{
    public function testHasDefaultPropertyValuesWithoutConfig()
    {
        $routerMock = $this->getMockForAbstractClass('\\fw\\control\\Router');
        
        $this->assertEquals('default',   $routerMock->getDefaultController());
        $this->assertEquals('index',     $routerMock->getDefaultAction());
        $this->assertEquals('error',     $routerMock->getErrorController());
        $this->assertEquals('exception', $routerMock->getExceptionAction());
        $this->assertEquals('not-found', $routerMock->getNotFoundAction());
    }
    
    public function testPropertiesCanBeLoadedFromConfig()
    {
        $configuration = new Configuration(array(
            'router' => array(
                'default_controller' => 'config-default-controller',
                'default_action'     => 'config-default-action',
                'error_controller'   => 'config-error-controller',
                'exception_action'   => 'config-exception-action',
                'not_found_action'   => 'config-not-found-action',
            )
        ));
        
        $routerMock = $this->getMockForAbstractClass('\\fw\\control\\Router', array($configuration));
        
        $this->assertEquals('config-default-controller', $routerMock->getDefaultController());
        $this->assertEquals('config-default-action',     $routerMock->getDefaultAction());
        $this->assertEquals('config-error-controller',   $routerMock->getErrorController());
        $this->assertEquals('config-exception-action',   $routerMock->getExceptionAction());
        $this->assertEquals('config-not-found-action',   $routerMock->getNotFoundAction());
    }
}