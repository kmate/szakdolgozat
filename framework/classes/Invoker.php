<?php

namespace fw;

use \ReflectionClass;
use \ReflectionException;
use \ReflectionMethod;
use \ReflectionParameter;

class Invoker
{
    private $_classInfo;
    private $_instance;
    
    public function __construct($className, array $constructorArguments = array())
    {
        try
        {
            $this->_classInfo = new ReflectionClass($className);
            
            if (null == $this->_classInfo->getConstructor())
            {
                $this->_instance = new $className();
            }
            else
            {
                $this->_instance = $this->_classInfo->newInstanceArgs($constructorArguments);
            }
        }
        catch (\Exception $ex)
        {
            throw new InvokerException(
                'Missing class: \'' . $className . '\'',
                InvokerException::MISSING_CLASS,
                $ex
            );
        }
    }
    
    public function checkInterface($interfaceName)
    {
        if (!$this->_classInfo->implementsInterface($interfaceName))
        {
            throw new InvokerException(
                'Not a(n) \'' . $interfaceName . '\' implementation: \'' . $this->_classInfo->getName() . '\'',
                InvokerException::INVALID_IMPLEMENTATION
            );
        }
    }
    
    public function checkParentClass($parentClassName)
    {
        if (!$this->_classInfo->isSubclassOf($parentClassName))
        {
            throw new InvokerException(
                'Not a(n) \'' . $parentClassName . '\' subclass: \'' . $this->_classInfo->getName() . '\'',
                InvokerException::INVALID_SUBCLASS
            );
        }
    }
    
    public function invoke($methodName, array $arguments = array())
    {
        try
        {
            $method    = $this->getMethod($methodName);
            $arguments = $this->matchArguments($arguments, $method);
            
            return $method->invokeArgs($this->_instance, $arguments);
        }
        catch (ReflectionException $ex)
        {
            throw new InvokerException(
                'Missing method: \'' . $this->_classInfo->getName() . '::' . $methodName . '\'',
                InvokerException::MISSING_METHOD,
                $ex
            );
        }
    }
    
    public function getMethod($methodName)
    {
        return new ReflectionMethod($this->_instance, $methodName);
    }
    
    public function matchArguments(array $arguments = array(), ReflectionMethod $method)
    {
        $methodName = $method->getName();
        
        $formalParameters = $method->getParameters();
        
        if (count($formalParameters) >= count($arguments))
        {
            if ($this->_isAssociative($arguments))
            {
                $arguments = $this->_reorderArgumentsByName($arguments, $formalParameters, $methodName);
            }
            
            $this->_checkArgumentTypes($arguments, $formalParameters, $methodName);
            
            return $arguments;
        }
        else
        {
            throw $this->_getInvalidParametersException($methodName);
        }
    }
    
    private function _getInvalidParametersException($methodName)
    {
        return new InvokerException(
            'Invalid parameters: \'' . $this->_classInfo->getName() . '::' . $methodName . '\'',
            InvokerException::INVALID_PARAMETERS
        );
    }
    
    private function _checkArgumentTypes(array $arguments, array $formalParameters, $methodName)
    {
        for ($i = 0; $i < count($arguments); ++$i)
        {
            if (!$this->_isValidArrayArgument($arguments[$i], $formalParameters[$i]) ||
                !$this->_isValidTypedArgument($arguments[$i], $formalParameters[$i]))
            {
                throw $this->_getInvalidParametersException($methodName);
            }
        }
    }
    
    private function _isValidArrayArgument($argument, ReflectionParameter $formalParameter)
    {
        return !$formalParameter->isArray() || is_array($argument);
    }
    
    private function _isValidTypedArgument($argument, ReflectionParameter $formalParameter)
    {
        $parameterClass = $formalParameter->getClass();
        
        return null == $parameterClass || (is_object($argument) && $parameterClass->isInstance($argument));
    }
    
    private function _reorderArgumentsByName(array $arguments, array $formalParameters, $methodName)
    {
        $newArguments = array();
        
        foreach ($formalParameters as $parameter)
        {
            if (isset($arguments[$parameter->getName()]))
            {
                $newArguments[$parameter->getPosition()] = $arguments[$parameter->getName()];
            }
            else if (!$parameter->isOptional())
            {
                throw $this->_getInvalidParametersException($methodName);
            }
        }
        
        return $newArguments;
    }
    
    private function _isAssociative(array $array)
    {
        return array_values($array) !== $array;
    }
}