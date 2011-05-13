<?php

namespace fw\tests\control;

use \fw\config\Configuration;
use \fw\control\RouteInfo;
use \fw\control\Router;
use \fw\control\UrlRouter;
use \PHPUnit_Framework_TestCase;

class UrlRouterTest extends PHPUnit_Framework_TestCase
{
    private $_router;
    
    public function setUp()
    {
        $this->_router = new UrlRouter();
    }
    
    public function tearDown()
    {
        unset($this->_router);
    }
    
    /**
     * @dataProvider parseRouteProvider
     */
    public function testRouteParsing($route, RouteInfo $expectedRoute)
    {
        $this->assertEquals($expectedRoute, $this->_router->parseRoute($route));
    }
    
    public function parseRouteProvider()
    {
        return array(
            array('test/index',  new RouteInfo('test', 'index')),
            array('/test/index', new RouteInfo('test', 'index')),
            array('/test/',      new RouteInfo('test', Router::DEFAULT_ACTION)),
            array('/',           new RouteInfo(Router::DEFAULT_CONTROLLER, Router::DEFAULT_ACTION)),
            array('',            new RouteInfo(Router::DEFAULT_CONTROLLER, Router::DEFAULT_ACTION))
        );
    }
    
    public function testRouteDefaultsToPathInfoServerVariable()
    {
        $_SERVER['PATH_INFO'] = 'test-controller/test-action/p1/v1';
        
        $expectedRoute = new RouteInfo('test-controller', 'test-action', array('p1' => 'v1'));
        
        $this->assertEquals($expectedRoute, $this->_router->parseRoute());
    }
    
    public function testExceptionRouteParsedAsNotFound()
    {
        $expectedRoute = new RouteInfo($this->_router->getErrorController(), $this->_router->getNotFoundAction());
        
        $this->assertEquals($expectedRoute, $this->_router->parseRoute('/error/exception'));
    }
    
    /**
     * @dataProvider parameterProvider
     */
    public function testParameterParsing($route, $parameters)
    {
        $this->assertEquals($parameters, $this->_router->parseRoute($route)->getParameters()->toArray());
    }
    
    public function parameterProvider()
    {
        return array(
            array(
                'test/index/p1/true/p2/42/p3/test string',
                array(
                    'p1' => true,
                    'p2' => 42,
                    'p3' => 'test string'
                )
            ),
            array(
                'test2/index/p1/v1/p2/',
                array(
                    'p1' => 'v1',
                    'p2' => null
                )
            ),
        );
    }
    
    public function testCanParseArrayParameters()
    {
        $route      = 'test/index/p1/true/p1/42/p2/v2/p1/test string/p3';
        $parameters = array(
            'p1' => array(true, 42, 'test string'),
            'p2' => 'v2',
            'p3' => null
        );
        
        $this->assertEquals($parameters, $this->_router->parseRoute($route)->getParameters()->toArray());
    }
    
    /**
     * @dataProvider generateRouteProvider
     */
    public function testGenerateRoute(RouteInfo $routeInfo, $route)
    {
        $this->assertEquals($route, $this->_router->generateRoute($routeInfo));
    }
    
    public function generateRouteProvider()
    {
        return array(
            array(
                new RouteInfo('controller', 'action', array()),
                '/controller/action'
            ),
            array(
                new RouteInfo('controller', 'action', array('p1' => 'v1', 'p2' => 'v2')),
                '/controller/action/p1/v1/p2/v2'
            ),
            array(
                new RouteInfo('controller', 'action', array('p1' => 'v1', 'p2' => array('v21', 'v22'))),
                '/controller/action/p1/v1/p2/v21/p2/v22'
            )
        );
    }
}