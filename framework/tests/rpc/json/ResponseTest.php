<?php

namespace fw\tests\rpc\json;

use fw\rpc\json\Error;
use fw\rpc\json\Response;
use \PHPUnit_Framework_TestCase;

class ResponseTest extends PHPUnit_Framework_TestCase
{
    private $_response;
    
    public function setUp()
    {
        $this->_response = new Response();
    }
    
    public function tearDown()
    {
        unset($this->_response);
    }
    
    public function testSettingResultUnsetsError()
    {
        $this->_response->setError(new Error());
        $this->_response->setResult(true);
        
        $this->assertNull($this->_response->getError());
    }
    
    public function testSettingErrorUnsetsResult()
    {
        $this->_response->setResult(true);
        $this->_response->setError(new Error());
        
        $this->assertNull($this->_response->getResult());
    }
    
    /**
     * @dataProvider responsesProvider
     */
    public function testEncodeResponse($response, $encodedData)
    {
        $this->assertEquals($encodedData, $response->encode());
    }
    
    public function responsesProvider()
    {
        return array(
            array(
                new Response(19, null, 1),
                '{"jsonrpc":"2.0","result":19,"id":1}'
            ),
            array(
                new Response(-19, null, 2),
                '{"jsonrpc":"2.0","result":-19,"id":2}'
            ),
            array(
                new Response(true, null, 0),
                '{"jsonrpc":"2.0","result":true,"id":0}'
            ),
            array(
                new Response(true, null, null),
                '{"jsonrpc":"2.0","result":true,"id":null}'
            ),
            array(
                new Response(null, new Error(-32601, 'Procedure not found.'), '1'),
                '{"jsonrpc":"2.0","error":{"code":-32601,"message":"Procedure not found."},"id":"1"}'
            ),
            array(
                new Response(null, new Error(-32601, 'Procedure not found.', array('unknown')), '1'),
                '{"jsonrpc":"2.0","error":{"code":-32601,"message":"Procedure not found.","data":["unknown"]},"id":"1"}'
            ),
            array(
                new Response(array('hello', 5), null, '9'),
                '{"jsonrpc":"2.0","result":["hello",5],"id":"9"}'
            ),
            array(
                new Response(),
                '{"jsonrpc":"2.0","error":{"code":-32603,"message":"Internal error."},"id":null}'
            ),
            array(
                new Response(null, null, 42),
                '{"jsonrpc":"2.0","error":{"code":-32603,"message":"Internal error."},"id":42}'
            )
        );
    }
}