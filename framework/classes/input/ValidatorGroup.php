<?php

namespace fw\input;

use \fw\KeyValueStorage;

/**
 * Csoportos bemenet-ellenőrző és szűrő
 * 
 * @author Karácsony Máté
 */
class ValidatorGroup extends Validator
{
    private $_keyValidators = array();
    private $_defaultValues = array();
    
    /**
     * Új bemenet-ellenőrző hozzáadása
     * 
     * @param  Validator       bemenet-ellenőrző
     * @param  string          érték kulcsa a bemeneti kollekcióban
     * @param  mixed           alapértelmezett érték
     * @return ValidatorGroup  önmagát adja vissza (láncolt híváshoz)
     */
    public function addValidator(Validator $validator, $key, $defaultValue = null)
    {
        $this->_keyValidators[$key][] = $validator;
        $this->_defaultValues[$key]   = $defaultValue;
        
        return $this;
    }
    
    /**
     * Ellenőrzés és szűrés végrehajtása
     * 
     * @param  mixed  a bemenetről kapott érték halmaza (asszociatív tömb vagy kulcs-érték tár)
     * @return bool   az ellenőrzés kimenete
     */
    public function validate(&$collection)
    {
        $this->_lastErrors = new KeyValueStorage();
        
        $converted = false;
        
        if ($collection instanceof KeyValueStorage)
        {
            $converted = true;
            
            $collection = $collection->toArray();
        }
        
        $isValid = $this->_validateArray($collection);
        
        if ($converted)
        {
            $collection = new KeyValueStorage($collection);
        }
        
        return $isValid;
    }
    
    private function _validateArray(array &$array)
    {
        $isValid = true;
        
        foreach ($this->_keyValidators as $key => $validators)
        {
            if (!$this->_validateArrayKey($validators, $array, $key))
            {
                $isValid = false;
            }
        }
        
        return $isValid;
    }
    
    private function _validateArrayKey($validators, &$array, $key)
    {
        $isValid = true;
        
        foreach ($validators as $validator)
        {
            if (!isset($array[$key]))
            {
                $array[$key] = $this->_defaultValues[$key];
            }
            
            if (!$validator->validate($array[$key]))
            {
                if (!isset($this->_lastErrors->$key))
                {
                    $this->_lastErrors->$key = array();
                }
                
                $this->_lastErrors->$key = array_merge(
                    $this->_lastErrors->$key->toArray(),
                    $validator->getLastErrors()
                );
                
                $isValid = false;
            }
        }
        
        return $isValid;
    }
}