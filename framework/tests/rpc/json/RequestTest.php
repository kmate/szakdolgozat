<?php

namespace fw\tests\rpc\json;

use fw\rpc\json\Request;
use \PHPUnit_Framework_TestCase;

class RequestTest extends PHPUnit_Framework_TestCase
{
    private $_request;
    
    public function setUp()
    {
        $this->_request = new Request();
    }
    
    public function tearDown()
    {
        unset($this->_request);
    }
    
    /**
     * @dataProvider validRequestsProvider
     */
    public function testDecodeFillsParameters($data, $version, $method, $parameters, $messageId)
    {
        $this->_request->decode($data);
        
        $this->assertTrue($this->_request->isValid());
        
        $this->assertEquals($version,    $this->_request->getVersion());
        $this->assertEquals($method,     $this->_request->getMethod());
        $this->assertEquals($parameters, $this->_request->getParameters());
        $this->assertEquals($messageId,  $this->_request->getMessageId());
    }
    
    public function validRequestsProvider()
    {
        return array(
            array(
                '{"jsonrpc": "2.0", "method": "subtract", "params": [42, 23], "id": 1}',
                '2.0',
                'subtract',
                array(42, 23),
                1
            ),
            array(
                '{"jsonrpc": "2.0", "method": "subtract", "params": {"subtrahend": 23, "minuend": 42}, "id": 3}',
                '2.0',
                'subtract',
                array('subtrahend' => 23, 'minuend' => 42),
                3
            ),
            array(
                '{"jsonrpc": "2.0", "method": "update", "params": [1,2,3,4,5]}',
                '2.0',
                'update',
                array(1, 2, 3, 4, 5),
                null
            )
        );
    }
    
    /**
     * @dataProvider invalidRequestsProvider
     */
    public function testInvalidRequestsAreRecognized($data)
    {
        $this->_request->decode($data);
        
        $this->assertFalse($this->_request->isValid());
    }
    
    public function invalidRequestsProvider()
    {
        return array(
            array('[]', 0),
            array('{"jsonrpc": "1.0", "method": "old-notification"}'),
            array('{"jsonrpc": "2.0", "method": 1, "params": "bar"}'),
            array('{"foo": "bar"}')
        );
    }
    
    /**
     * @dataProvider validBatchRequestsProvider
     */
    public function testBatchDecodingNotSupported($data)
    {
        $this->_request->decode($data);
        
        $this->assertFalse($this->_request->isValid());
    }
    
    public function validBatchRequestsProvider()
    {
        return array(
            array(
                '[' .
                '    {"jsonrpc": "2.0", "method": "sum", "params": [1,2,4], "id": "1"},' .
                '    {"jsonrpc": "2.0", "method": "notify_hello", "params": [7]},' .
                '    {"jsonrpc": "2.0", "method": "subtract", "params": [42,23], "id": "2"},' .
                '    {"jsonrpc": "2.0", "method": "foo.get", "params": {"name": "myself"}, "id": "5"},' .
                '    {"jsonrpc": "2.0", "method": "get_data", "id": "9"} ' .
                ']'
            ),
            array(
                '[' .
                '    {"jsonrpc": "2.0", "method": "notify_sum", "params": [1,2,4]},' .
                '    {"jsonrpc": "2.0", "method": "notify_hello", "params": [7]} ' .
                ']'
            ),
            array('[1]'),
            array('[1,2,3]')
        );
    }
    
    /**
     * @dataProvider invalidJsonProvider
     * 
     * @expectedException     \fw\rpc\Exception
     * @expectedExceptionCode 1 (Exception::REQUEST_DECODE_ERROR)
     */
    public function testDecodeThrowsExceptionOnJsonParseError($data)
    {
        $this->_request->decode($data);
    }
    
    public function invalidJsonProvider()
    {
        return array(
            array('{"jsonrpc": "2.0", "method": "foobar, "params": "bar", "baz]'),
            array('[ {"jsonrpc": "2.0", "method": "sum", "params": [1,2,4], "id": "1"},{"jsonrpc": "2.0", "method" ]')
        );
    }
}