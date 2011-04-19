<?php

namespace fw\tests\control;

use \fw\KeyValueStorage;
use \fw\control\RouteInfo;
use \PHPUnit_Framework_TestCase;

class RouteInfoTest extends PHPUnit_Framework_TestCase
{
    public function testControllerNameCanBeSet()
    {
        $routeInfo = new RouteInfo('testController', '');
        
        $this->assertEquals('testController', $routeInfo->getControllerName());
    }
    
    public function testActionNameCanBeSet()
    {
        $routeInfo = new RouteInfo('', 'testAction');
        
        $this->assertEquals('testAction', $routeInfo->getActionName());
    }
    
    public function testConstructorWorksWithArrayParameters()
    {
        $routeInfo = new RouteInfo('', '', array('a' => 'b'));
        
        $this->assertEquals('b', $routeInfo->getParameters()->get('a'));
    }
    
    public function testConstructorWorksWithKeyValueStorageParameters()
    {
        $parameters = new KeyValueStorage();
        $parameters->set('a', 'b');
        
        $routeInfo = new RouteInfo('', '', $parameters);
        
        $this->assertEquals('b', $routeInfo->getParameters()->get('a'));
    }
    
    public function testConstructorCreatesKeyValueStorageOnIncorrectParameters()
    {
        $routeInfo = new RouteInfo('', '', 42);
        
        $this->assertInstanceOf('\fw\KeyValueStorage', $routeInfo->getParameters());
    }
}
