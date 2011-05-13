<?php

namespace fw\tests\view;

use \fw\config\Configuration;
use \fw\control\RouteInfo;
use \fw\view\Template;
use \fw\view\TemplateView;
use \PHPUnit_Extensions_OutputTestCase;

if (!defined('VIEW_TEST_ASSETS_PATH'))
{
    define('VIEW_TEST_ASSETS_PATH', TEST_ASSETS_PATH . DIRECTORY_SEPARATOR . 'view');
}

class TemplateViewTest extends PHPUnit_Extensions_OutputTestCase
{
    private $_view;
    
    public function setUp()
    {
        $configuration = new Configuration(array(
            'view' => array(
                'template_directory' => VIEW_TEST_ASSETS_PATH
            )
        ));
        
        $this->_view = new TemplateView($configuration);
    }
    
    public function tearDown()
    {
        unset($this->_view);
    }
    
    public function testParseActionTemplateNameFromRouteInfo()
    {
        $controllerName       = 'controller-name';
        $actionName           = 'action-name';
        $routeInfo            = $this->_createTestRouteInfo($controllerName, $actionName);
        $expectedTemplateName = $controllerName . DIRECTORY_SEPARATOR . $actionName;
        
        $parsedTemplateName = $this->_view->parseActionTemplateName($routeInfo);
        
        $this->assertEquals($expectedTemplateName, $parsedTemplateName);
    }
    
    public function testParseLayoutTemplateNameDefault()
    {
        $routeInfo            = $this->_createTestRouteInfo();
        $expectedTemplateName = TemplateView::DEFAULT_LAYOUT_TEMPLATE;
        
        $this->_view = new TemplateView();
        $parsedTemplateName = $this->_view->parseLayoutTemplateName($routeInfo);
        
        $this->assertEquals($expectedTemplateName, $parsedTemplateName);
    }
    
    public function testParseDefaultLayoutTemplateNameFromConfiguration()
    {
        $routeInfo            = $this->_createTestRouteInfo();
        $expectedTemplateName = 'test_layout_default';
        
        $configuration = new Configuration(array(
            'view' => array(
                'default_layout' => 'test_layout_default'
            )
        ));
        
        $this->_view = new TemplateView($configuration);
        $parsedTemplateName = $this->_view->parseLayoutTemplateName($routeInfo);
        
        $this->assertEquals($expectedTemplateName, $parsedTemplateName);
    }
    
    public function testParseLayoutTemplateNameFromConfiguration()
    {
        $controllerName       = 'controller-name';
        $actionName           = 'action-name';
        $routeInfo            = $this->_createTestRouteInfo($controllerName, $actionName);
        $expectedTemplateName = 'test_layout';
        
        $configuration = new Configuration(array(
            'view' => array(
                'layout' => array(
                    $controllerName => array(
                        $actionName => 'test_layout'
                    )
                )
            )
        ));
        
        $this->_view = new TemplateView($configuration);
        $parsedTemplateName = $this->_view->parseLayoutTemplateName($routeInfo);
        
        $this->assertEquals($expectedTemplateName, $parsedTemplateName);
    }
    
    public function testSetTemplatesByRouteInfo()
    {
        $routeInfo = $this->_createTestRouteInfo();
        $this->_view->setTemplatesByRouteInfo($routeInfo);
        
        $this->assertInstanceOf('\\fw\view\\Template', $this->_view->getLayoutTemplate());
        $this->assertInstanceOf('\\fw\view\\Template', $this->_view->getActionTemplate());
    }
    
    public function testChangeLayoutTemplate()
    {
        $routeInfo = $this->_createTestRouteInfo();
        $this->_view->setTemplatesByRouteInfo($routeInfo);
        $this->_view->setLayoutTemplate($this->_view->createTemplate('layout_variable'));
        $this->_view->getLayoutTemplate()->variable = 'value';
        $this->_view->render();
        
        $this->expectOutputString('value');
    }
    
    public function testChangeActionTemplate()
    {
        $routeInfo = $this->_createTestRouteInfo();
        $this->_view->setTemplatesByRouteInfo($routeInfo);
        $this->_view->setActionTemplate($this->_view->createTemplate('render_test'));
        $this->_view->render();
        
        $this->expectOutputString('layout-test');
    }
    
    public function testRenderAppliesLayoutOnActionTemplate()
    {
        $routeInfo = $this->_createTestRouteInfo();
        $this->_view->setTemplatesByRouteInfo($routeInfo);
        
        $this->_view->render();
        
        $this->expectOutputString('layout-test');
    }
    
    public function testRenderReturnsOutput()
    {
        $routeInfo = $this->_createTestRouteInfo();
        $this->_view->setTemplatesByRouteInfo($routeInfo);
        
        $output = $this->_view->render(true);
        
        $this->assertEquals('layout-test', $output);
    }
    
    public function testLayoutReadsVariablesFromConfiguration()
    {
        $routeInfo = $this->_createTestRouteInfo();
        
        $configuration = new Configuration(array(
            'view' => array(
                'template_directory' => VIEW_TEST_ASSETS_PATH,
                'default_layout'     => 'layout_variable',
                'layout_variables'   => array('variable' => 'value')
            )
        ));
        
        $this->_view = new TemplateView($configuration);
        $this->_view->setTemplatesByRouteInfo($routeInfo);
        $this->_view->render();
        
        $this->expectOutputString('value');
    }
    
    private function _createTestRouteInfo($controllerName = 'test-controller', $actionName = 'test-action')
    {
        return new RouteInfo($controllerName, $actionName);
    }
}