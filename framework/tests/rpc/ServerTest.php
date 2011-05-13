<?php

namespace fw\tests\rpc;

use fw\rpc\Exception;
use fw\rpc\Request;
use fw\rpc\Response;
use fw\rpc\Server;
use \PHPUnit_Framework_TestCase;

class ServerTest extends PHPUnit_Framework_TestCase
{
    public function testHandleCallsParseErrorHandler()
    {
        $serverMock = $this->_getServerMock(array('_decodeRequest'));
        $serverMock->expects($this->once())
                   ->method('_decodeRequest')
                   ->will($this->throwException(Exception::create(Exception::PARSE_ERROR)));
        
        $serverMock->expects($this->once())
                   ->method('_handleParseError');
        
        $serverMock->handle('');
    }
    
    public function testHandleCallsInvalidRequestHandler()
    {
        $serverMock = $this->_getServerMock(array('_decodeRequest'));
        $serverMock->expects($this->once())
                   ->method('_decodeRequest')
                   ->will($this->throwException(Exception::create(Exception::INVALID_REQUEST)));
        
        $serverMock->expects($this->once())
                   ->method('_handleInvalidRequest');
        
        $serverMock->handle('');
    }
    
    public function testHandleDoesNotTryInvokeServiceAfterDecodeError()
    {
        $serverMock = $this->_getServerMock(array('_tryDecodeRequest', '_tryInvokeService'));
        
        $this->_setDecodeSuccessful($serverMock, false);
        
        $serverMock->expects($this->never())
                   ->method('_tryInvokeService');
        
        $serverMock->handle('');
    }
    
    public function testInvokeServiceCalledOnHandle()
    {
        $serverMock = $this->_getServerMock(array('_tryDecodeRequest'));
        
        $this->_setDecodeSuccessful($serverMock, true);
        
        $serverMock->expects($this->once())
                   ->method('_invokeService');
        
        $serverMock->handle('');
    }
    
    public function testInvokeServiceCallsMethodNotFoundHandler()
    {
        $serverMock = $this->_getServerMock(array('_tryDecodeRequest'));
        
        $this->_setDecodeSuccessful($serverMock, true);
        
        $serverMock->expects($this->once())
                   ->method('_invokeService')
                   ->will($this->throwException(Exception::create(Exception::METHOD_NOT_FOUND)));
        
        $serverMock->expects($this->once())
                   ->method('_handleMethodNotFound');
        
        $serverMock->handle('');
    }
    
    public function testInvokeServiceCallsInvalidParamsHandler()
    {
        $serverMock = $this->_getServerMock(array('_tryDecodeRequest'));
        
        $this->_setDecodeSuccessful($serverMock, true);
        
        $serverMock->expects($this->once())
                   ->method('_invokeService')
                   ->will($this->throwException(Exception::create(Exception::INVALID_PARAMS)));
        
        $serverMock->expects($this->once())
                   ->method('_handleInvalidParams');
        
        $serverMock->handle('');
    }
    
    public function testInvokeServiceCallsInternalErrorHandler()
    {
        $serverMock = $this->_getServerMock(array('_tryDecodeRequest'));
        
        $this->_setDecodeSuccessful($serverMock, true);
        
        $serverMock->expects($this->once())
                   ->method('_invokeService')
                   ->will($this->throwException(Exception::create(Exception::INTERNAL_ERROR)));
        
        $serverMock->expects($this->once())
                   ->method('_handleInternalError');
        
        $serverMock->handle('');
    }
    
    public function testInvokeServiceCallsApplyResultFunction()
    {
        $invokeResult = 'test-result';
        
        $serverMock = $this->_getServerMock(array('_tryDecodeRequest'));
        
        $this->_setDecodeSuccessful($serverMock, true);
        
        $serverMock->expects($this->once())
                   ->method('_invokeService')
                   ->will($this->returnValue($invokeResult));
        
        $serverMock->expects($this->once())
                   ->method('_applyInvokeResult')
                   ->with($invokeResult);
        
        $serverMock->handle('');
    }
    
    private function _getServerMock(array $stubbedMethods = array())
    {
        $requestMock  = $this->getMock('\\fw\\rpc\\Request');
        $responseMock = $this->getMock('\\fw\\rpc\\Response');
        
        $abstractMethods = array(
            '_invokeService',
            '_applyInvokeResult',
            '_handleParseError',
            '_handleInvalidRequest',
            '_handleMethodNotFound',
            '_handleInvalidParams',
            '_handleInternalError'
        );
        
        $stubbedMethods = array_unique(array_merge($abstractMethods, $stubbedMethods));
        
        $serverMock = $this->getMock('\\fw\\rpc\\Server', $stubbedMethods);
        $serverMock->setRequest($requestMock);
        $serverMock->setResponse($responseMock);
        
        return $serverMock;
    }
    
    private function _setDecodeSuccessful($serverMock, $decodeSuccesful)
    {
        $serverMock->expects($this->once())
                   ->method('_tryDecodeRequest')
                   ->will($this->returnValue($decodeSuccesful));
    }
}