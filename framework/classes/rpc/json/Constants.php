<?php

namespace fw\rpc\json;

final class Constants
{
    const VERSION = '2.0';
    
    const PARSE_ERROR_CODE    = -32700;
    const PARSE_ERROR_MESSAGE = 'Parse error.';
    
    const INVALID_REQUEST_CODE    = -32600;
    const INVALID_REQUEST_MESSAGE = 'Invalid Request.';
    
    const METHOD_NOT_FOUND_CODE    = -32601;
    const METHOD_NOT_FOUND_MESSAGE = 'Method not found.';
    
    const INVALID_PARAMS_CODE    = -32602;
    const INVALID_PARAMS_MESSAGE = 'Invalid params.';
    
    const INTERNAL_ERROR_CODE    = -32603;
    const INTERNAL_ERROR_MESSAGE = 'Internal error.';
}