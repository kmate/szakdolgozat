<?php

namespace fw\tests\rpc\json;

use \fw\config\Configuration;
use \fw\rpc\Exception;
use \fw\rpc\json\Constants;
use \fw\rpc\json\Error;
use \fw\rpc\json\Request;
use \fw\rpc\json\Response;
use \fw\rpc\json\Server;
use \PHPUnit_Framework_TestCase;

if (!defined('RPC_TEST_ASSETS_PATH'))
{
    define('RPC_TEST_ASSETS_PATH', TEST_ASSETS_PATH . DIRECTORY_SEPARATOR . 'rpc');
}

define('JSON_RPC_TEST_ASSETS_PATH', RPC_TEST_ASSETS_PATH . DIRECTORY_SEPARATOR . 'json');

class ServerTest extends PHPUnit_Framework_TestCase
{
    const JSON_PARSE_ERROR_DATA          = '{';
    const JSON_BATCH_REQUEST_DATA        = '[1]';
    const JSON_INVALID_REQUEST_DATA      = '{"jsonrpc":1,"id":1}';
    const JSON_MISSING_SERVICE_CALL_DATA = '{"jsonrpc":"2.0","method":"MissingService.method","params":[],"id":1}';
    const JSON_INVALID_SERVICE_CALL_DATA = '{"jsonrpc":"2.0","method":"InvalidService.method","params":[""],"id":1}';
    const JSON_MISSING_METHOD_CALL_DATA  = '{"jsonrpc":"2.0","method":"TestService.missingMethod","params":[],"id":1}';
    const JSON_INVALID_PARAMS_CALL_DATA  = '{"jsonrpc":"2.0","method":"TestService.arrayOnly","params":[""],"id":1}';
    const JSON_EXCEPTION_CALL_DATA       = '{"jsonrpc":"2.0","method":"TestService.throwException","params":[],"id":1}';
    const JSON_SEND_BACK_CALL_DATA       = '{"jsonrpc":"2.0","method":"TestService.sendBack","params":["test"],"id":1}';
    const JSON_RETURN_CONFIG_CALL_DATA   = '{"jsonrpc":"2.0","method":"TestService.returnConfig","params":[],"id":1}';
    
    private $_server;
    
    public function setUp()
    {
        $configuration = new Configuration(array(
            'rpc' => array(
                'services' => array(
                    'classpath' => RPC_TEST_ASSETS_PATH
                )
            )
        ));
        
        $this->_server = new Server($configuration);
    }
    
    public function tearDown()
    {
        unset($this->_server);
    }
    
    public function testHandleRequestDecodeErrorSetsResponseParameters()
    {
        $output = $this->_server->handle(self::JSON_PARSE_ERROR_DATA);
        
        $this->assertEquals(Error::create(Constants::PARSE_ERROR_CODE), $this->_server->getResponse()->getError());
    }
    
    public function testHandleInvalidRequestErrorSetsResponseParameters()
    {
        $output = $this->_server->handle(self::JSON_INVALID_REQUEST_DATA);
        
        $this->assertEquals(Error::create(Constants::INVALID_REQUEST_CODE), $this->_server->getResponse()->getError());
    }
    
    public function testHandleSetsResponseMessageId()
    {
        $expectedMessageId = 'test-message-id';
        
        $this->_server->handle('{"jsonrpc":"2.0","method":"test","params":[],"id":"' . $expectedMessageId . '"}');
        
        $this->assertEquals($expectedMessageId, $this->_server->getResponse()->getMessageId());
    }
    
    /**
     * @dataProvider invalidMethodNameProvider
     */
    public function testHandleCallsMethodNotFoundHandler($invalidMethodName)
    {
        $requestStub = $this->_getValidRequestStubForMethod($invalidMethodName);
        
        $this->_server->setRequest($requestStub);
        $this->_server->handle('{}');
        
        $this->assertEquals(Error::create(Constants::METHOD_NOT_FOUND_CODE), $this->_server->getResponse()->getError());
    }
    
    public function invalidMethodNameProvider()
    {
        return array(
            array(''),
            array('0'),
            array('é?'),
            array('a+b'),
            array('a-b'),
            array('class:method'),
            array('relative\\namespace\\class::someMethod'),
        );
    }
    
    public function testApplyInvokeResultSetsResponseResult()
    {
        $expectedResult = 'test-result';
        
        $requestStub = $this->_getValidRequestStub();
        
        $serverStub = $this->getMock('\\fw\\rpc\\json\\Server', array('_invokeService'));
        $serverStub->expects($this->once())
                   ->method('_invokeService')
                   ->will($this->returnValue($expectedResult));
        $serverStub->setRequest($requestStub);
        $serverStub->handle('');
        
        $this->assertEquals($expectedResult, $serverStub->getResponse()->getResult());
    }
    
    public function testSetsInternalErrorWhenExceptionThrownByService()
    {
        $this->_server->handle(self::JSON_EXCEPTION_CALL_DATA);
        
        $this->assertEquals(Error::create(Constants::INTERNAL_ERROR_CODE), $this->_server->getResponse()->getError());
    }
    
    public function testSetsResponseResultWhenServiceCallSucceeded()
    {
        $configuration = new Configuration(array(
            'rpc' => array(
                'services' => array(
                    'classpath' => JSON_RPC_TEST_ASSETS_PATH,
                    'namespace' => 'json'
                )
            )
        ));
        
        $this->_server = new Server($configuration);
        $this->_server->handle(self::JSON_SEND_BACK_CALL_DATA);
        
        $this->assertEquals('test', $this->_server->getResponse()->getResult());
    }
    
    public function testConfigurationIsInjectedIntoService()
    {
        $configuration = new Configuration(array(
            'rpc' => array(
                'services' => array(
                    'classpath' => JSON_RPC_TEST_ASSETS_PATH,
                    'namespace' => 'json'
                )
            )
        ));
        
        $this->_server = new Server($configuration);
        $this->_server->handle(self::JSON_RETURN_CONFIG_CALL_DATA);
        
        $this->assertEquals($configuration, $this->_server->getResponse()->getResult());
    }
    
    public function testMethodNotFoundErrorReturnedWhenServiceIsMissing()
    {
        $this->_server->handle(self::JSON_MISSING_SERVICE_CALL_DATA);
        
        $this->assertEquals(Error::create(Constants::METHOD_NOT_FOUND_CODE), $this->_server->getResponse()->getError());
    }
    
    public function testSetsInternalErrorWhenServiceImplementationIsInvalid()
    {
        $this->_server->handle(self::JSON_INVALID_SERVICE_CALL_DATA);
        
        $this->assertEquals(Error::create(Constants::INTERNAL_ERROR_CODE), $this->_server->getResponse()->getError());
    }
    
    public function testMethodNotFoundErrorReturnedWhenMethodIsMissing()
    {
        $this->_server->handle(self::JSON_MISSING_METHOD_CALL_DATA);
        
        $this->assertEquals(Error::create(Constants::METHOD_NOT_FOUND_CODE), $this->_server->getResponse()->getError());
    }
    
    public function testInvalidParamsErrorReturnedWhenParametersAreWrong()
    {
        $this->_server->handle(self::JSON_INVALID_PARAMS_CALL_DATA);
        
        $this->assertEquals(Error::create(Constants::INVALID_PARAMS_CODE), $this->_server->getResponse()->getError());
    }
    
    private function _getValidRequestStub()
    {
        $requestStub = $this->getMock('\\fw\\rpc\\json\\Request', array('decode', 'isValid', 'getMethod'));
        $requestStub->expects($this->once())
                    ->method('isValid')
                    ->will($this->returnValue(true));
        
        return $requestStub;
    }
    
    private function _getValidRequestStubForMethod($method)
    {
        $requestStub = $this->_getValidRequestStub();
        $requestStub->expects($this->once())
                    ->method('getMethod')
                    ->will($this->returnValue($method));
        
        return $requestStub;
    }
}