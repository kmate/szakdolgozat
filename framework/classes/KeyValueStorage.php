<?php

namespace fw;

/**
 * Kulcs-érték tár
 * (asszociatív tömbök burkolására, rekurzív struktúra)
 * 
 * @author Karácsony Máté
 */
class KeyValueStorage
{
    protected $_transformedKeys;
    protected $_data;
    
    /**
     * Létrehoz egy új kulcs-érték tárat a megadott tömb alapján
     * (a tömböket áttranszformálja kulcs-érték tárakra a legalsó szintig)
     * 
     * @param array  a beburkolandó tömb
     */
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
    
    /**
     * Megvizsgálja, hogy létezik-e adat a megadott kulcson
     * 
     * @param  string  a vizsgált kulcs
     * @return bool
     */
    public function has($key)
    {
        return isset($this->_data[$key]);
    }
    
    /**
     * Megvizsgálja, hogy létezik-e adat a megadott kulcson (alternatív szintaxis)
     * 
     * @param  string  a vizsgált kulcs
     * @return bool
     */
    public function __isset($key)
    {
        return $this->has($key);
    }
    
    /**
     * Lekéri az adott kulcson lévő adatot
     * 
     * @param  string  a lekért kulcs
     * @return mixed   a keresett vagy az alapértelmezett érték (ha a megadott kulcson nincs adat)
     */
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
    
    /**
     * Lekéri az adott kulcson lévő adatot (alternatív szintaxis)
     * 
     * @param  string  a lekért kulcs
     * @return mixed   a keresett vagy az alapértelmezett érték (ha a megadott kulcson nincs adat)
     */
    public function __get($key)
    {
        return $this->get($key);
    }
    
    /**
     * Beállítja az adott kulcson lévő adatot
     * (tömb esetén áttranszformálja kulcs-érték tárrá)
     * 
     * @param  string           a beállítandó kulcs
     * @param  mixed            a beállítandó érték
     * @return KeyValueStorage  önmagával tér vissza (láncolt hívásokhoz)
     */
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
    
    /**
     * Beállítja az adott kulcson lévő adatot (alternatív szintaxis)
     * (tömb esetén áttranszformálja kulcs-érték tárrá)
     * 
     * @param  string           a beállítandó kulcs
     * @param  mixed            a beállítandó érték
     * @return KeyValueStorage  önmagával tér vissza (láncolt hívásokhoz)
     */
    public function __set($key, $value)
    {
        return $this->set($key, $value);
    }
    
    /**
     * Tömbbé transzformálja a kulcs-érték tárat a megadott mélységig
     * (a megadott mélységen túl kulcs-érték tárak maradnak a visszaadott struktúrában)
     *
     * @param  int    az átalakítás mélysége
     * @return array  a transzformált struktúra
     */
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
