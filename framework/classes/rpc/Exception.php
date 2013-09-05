<?php

namespace fw\rpc;

/**
 * Távoli eljáráshívás kivételek ősosztálya
 * 
 * @author Karácsony Máté
 */
class Exception extends \fw\Exception
{
    const PARSE_ERROR      = 1;
    const INVALID_REQUEST  = 2;
    const METHOD_NOT_FOUND = 3;
    const INVALID_PARAMS   = 4;
    const INTERNAL_ERROR   = 5;
    
    /**
     * Kivételt készít a megadott kód és előzmény-kivétel alapján (gyártó függvény)
     * 
     * @param  int         a kivétel kódja
     * @param  \Exception  előzmény-kivétel
     * @return Exception
     */
    public static function create($code, \Exception $cause = null)
    {
        switch ($code)
        {
            case self::PARSE_ERROR:
                $exception = new Exception('Unable to parse request data.', self::PARSE_ERROR, $cause);
                break;
            
            case self::INVALID_REQUEST:
                $exception = new Exception('Invalid request data.', self::INVALID_REQUEST, $cause);
                break;
            
            case self::METHOD_NOT_FOUND:
                $exception = new Exception('Method not found.', self::METHOD_NOT_FOUND, $cause);
                break;
            
            case self::INVALID_PARAMS:
                $exception = new Exception('Invalid parameters.', self::INVALID_PARAMS, $cause);
                break;
            
            default:
            case self::INTERNAL_ERROR:
                $exception = new Exception('Internal error.', self::INTERNAL_ERROR, $cause);
        }
        
        return $exception;
    }
}
