<?php

namespace fw\tests\view;

use \fw\control\RouteInfo;
use \fw\view\View;
use \fw\view\ViewUtils;
use \PHPUnit_Framework_TestCase;

class ViewTest extends PHPUnit_Framework_TestCase
{
    private $_view;
    
    public function setUp()
    {
        $this->_view = $this->getMockForAbstractClass('\\fw\\view\\View');
    }
    
    public function tearDown()
    {
        unset($this->_view);
    }
    
    public function testDefaultIsAutoRenderTurnedOn()
    {
        $this->_view->expects($this->once())->method('_render');
        $this->_view->runAutoRenderer();
    }
    
    public function testAutoRenderRunsOnce()
    {
        $this->_view->expects($this->once())->method('_render')->with(false);
        
        $this->_view->runAutoRenderer();
        $this->_view->runAutoRenderer();
    }
    
    public function testGenerateLinkInvokesRouter()
    {
        $_SERVER['SCRIPT_NAME'] = '';
        
        $routerMock = $this->getMockForAbstractClass('\\fw\\control\\Router');
        $routerMock->expects($this->once())
                   ->method('generateRoute')
                   ->will($this->returnCallback(array($this, 'linkGeneratorCallback')));
        
        ViewUtils::setRouter($routerMock);
        
        $this->assertEquals('/controller/action', $this->_view->generateLink('controller', 'action'));
    }
    
    public function linkGeneratorCallback(RouteInfo $routeInfo)
    {
        return '/' . $routeInfo->getControllerName() . '/' . $routeInfo->getActionName();
    }
    
    public function testBaseHrefGetterReturnsCorrectProtocol()
    {
        $_SERVER['HTTP_HOST'] = 'localhost';
        
        unset($_SERVER['HTTPS']);
        
        $this->assertEquals('http://localhost', $this->_view->getBaseHref());
        
        $_SERVER['HTTPS'] = 'on';
        
        $this->assertEquals('https://localhost', $this->_view->getBaseHref());
    }
    
    public function testBaseHrefGetterReturnsServerDataWithoutHostHeader()
    {
        unset($_SERVER['HTTP_HOST']);
        unset($_SERVER['HTTPS']);
        
        $_SERVER['SERVER_NAME'] = 'localhost';
        $_SERVER['SERVER_PORT'] = 80;
        
        $this->assertEquals('http://localhost:80', $this->_view->getBaseHref());
    }
}