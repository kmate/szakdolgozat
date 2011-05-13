<?php

namespace fw\rpc\json;

class Error
{
    public $code;
    public $message;
    public $data;
    
    public function __construct($code = 0, $message = '', $data = null)
    {
        $this->code    = $code;
        $this->message = $message;
        $this->data    = $data;
    }
    
    public static function create($code, $data = null)
    {
        switch ($code)
        {
            case Constants::PARSE_ERROR_CODE:
                $error = new Error(Constants::PARSE_ERROR_CODE, Constants::PARSE_ERROR_MESSAGE, $data);
                break;
            
            case Constants::INVALID_REQUEST_CODE:
                $error = new Error(Constants::INVALID_REQUEST_CODE, Constants::INVALID_REQUEST_MESSAGE, $data);
                break;
            
            case Constants::METHOD_NOT_FOUND_CODE:
                $error = new Error(Constants::METHOD_NOT_FOUND_CODE, Constants::METHOD_NOT_FOUND_MESSAGE, $data);
                break;
            
            case Constants::INVALID_PARAMS_CODE:
                $error = new Error(Constants::INVALID_PARAMS_CODE, Constants::INVALID_PARAMS_MESSAGE, $data);
                break;
            
            default:
            case Constants::INTERNAL_ERROR_CODE:
                $error = new Error(Constants::INTERNAL_ERROR_CODE, Constants::INTERNAL_ERROR_MESSAGE, $data);
        }
        
        return $error;
    }
}