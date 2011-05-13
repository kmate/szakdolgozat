<?php

namespace fw;

class KeyValueStorage
{
    protected $_transformedKeys;
    protected $_data;
    
    public function __construct(array $data = array())
    {
        $this->_transformedKeys = array();
        
        $this->_data = $this->_transformData($data);
    }
    
    protected function _transformData($data)
    {
        $transformedData = array();
        
        foreach ($data as $key => $value)
        {
            if (is_array($value))
            {
                $this->_transformedKeys[$key] = true;
                
                $transformedData[$key] = new KeyValueStorage($value);
            }
            else
            {
                $transformedData[$key] = $value;
            }
        }
        
        return $transformedData;
    }
    
    public function has($key)
    {
        return isset($this->_data[$key]);
    }
    
    public function __isset($key)
    {
        return $this->has($key);
    }
    
    public function get($key, $defaultValue = null)
    {
        if ($this->has($key))
        {
            return $this->_data[$key];
        }
        
        if (null === $defaultValue)
        {
            return new KeyValueStorage();
        }
        
        return $defaultValue;
    }
    
    public function __get($key)
    {
        return $this->get($key);
    }
    
    public function set($key, $value)
    {
        if (is_array($value))
        {
            $value = new KeyValueStorage($value);
            
            $this->_transformedKeys[$key] = true;
        }
        else
        {
            unset($this->_transformedKeys[$key]);
        }
        
        $this->_data[$key] = $value;
        
        return $this;
    }
    
    public function __set($key, $value)
    {
        return $this->set($key, $value);
    }
    
    public function toArray($maxDepth = null)
    {
        $result = array();
        
        foreach ($this->_data as $key => $value)
        {
            if ($value instanceof KeyValueStorage &&
                (!is_int($maxDepth) || $maxDepth > 0 || isset($this->_transformedKeys[$key])))
            {
                $result[$key] = $value->toArray(is_int($maxDepth) ? --$maxDepth : null);
            }
            else
            {
                $result[$key] = $value;
            }
        }
        
        return $result;
    }
}
