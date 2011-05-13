<?php

namespace fw\control;

abstract class Controller
{
    protected $_context;
    
    public function __construct(Context $context)
    {
        $this->_context = $context;
    }
    
    public function __get($propertyName)
    {
        if ('context' === $propertyName)
        {
            return $this->_context;
        }
        else
        {
            return $this->_context->{$propertyName};
        }
    }
}