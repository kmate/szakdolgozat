<?php

namespace app\model;

class DatabaseModelException extends Exception
{
    const DATASOURCE_NOT_FOUND   = 1;
    const UNIQUE_CONSTRAINT_FAIL = 2;
    
    public $fieldName;
    
    public static function uniqueConstraintFail($fieldName)
    {
        $exception = new DatabaseModelException(
            'Unique constraint check failed on field: \'' . $fieldName . '\'',
            self::UNIQUE_CONSTRAINT_FAIL
        );
        $exception->fieldName = $fieldName;
        
        return $exception;
    }
}