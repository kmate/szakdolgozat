<?php

namespace fw\config;

use \fw\KeyValueStorage;

/**
 * Konfigurációs struktúra
 * 
 * @author Karácsony Máté
 */
class Configuration extends KeyValueStorage
{
    const DEFAULT_SECTION = 'default';
    
    private static $_activeSection = self::DEFAULT_SECTION;
    
    public static function getActiveSection()
    {
        return self::$_activeSection;
    }
    
    /**
     * Átállítja az aktív konfigurációs szekciót, ha érvényes a megadott név
     * 
     * @param  string     az új szekció neve
     * @return string     az előzőleg aktív szekció neve
     * @throws Exception  az új szekció-név érvénytelen
     */
    public static function setActiveSection($newActiveSection)
    {
        $newActiveSection = trim($newActiveSection);
        $oldActiveSection = self::$_activeSection;
        
        if (self::isValidSectionName($newActiveSection))
        {
            self::$_activeSection = $newActiveSection;
        }
        else
        {
            self::_throwInvalidSectionNameException($newActiveSection);
        }
        
        return $oldActiveSection;
    }
    
    /**
     * Ellenőrzi egy szekció-név érvényességét
     * 
     * @param  string  az ellenőrizendő szekció-név
     * @return bool    
     */
    public static function isValidSectionName($sectionName)
    {
        return 1 === preg_match('/^[a-z][_a-z0-9]+$/i', $sectionName);
    }
    
    protected static function _throwInvalidSectionNameException($sectionName)
    {
        throw new Exception(
            'Invalid section name: \'' . $sectionName . '\'',
            Exception::INVALID_SECTION_NAME
        );
    }
    
    /**
     * Új konfigurációs struktúrát épít egy tömbből
     * (ha a második paraméter hamis, akkor az aktív szekcióba töltődnek az adatok,
     *  ha igaz, akkor a szekciók a legfelső szintű kulcsaiból keletkeznek)
     * 
     * @param array  a betöltendő adatok
     * @param bool   az adatok tartalmaznak-e szekciókat
     */
    public function __construct(array $data = array(), $withSections = false)
    {
        parent::__construct(
            $withSections
                ? $data
                : array(self::$_activeSection => $data)
        );
    }
    
    /**
     * Megvizsgálja, hogy létezik-e adat a megadott kulcson (az aktív szekcióban)
     * 
     * @param  array  a vizsgált kulcs
     * @return bool
     */
    public function has($key)
    {
        $this->_ensureActiveSectionKeyExists();
        
        return $this->_data[self::$_activeSection]->has($key);
    }
    
    /**
     * Lekéri az adott kulcson lévő adatot (az aktív szekcióból)
     * 
     * @param  string  a lekért kulcs
     * @return mixed   a keresett vagy az alapértelmezett érték (ha a megadott kulcson nincs adat)
     */
    public function get($key, $defaultValue = null)
    {
        $this->_ensureActiveSectionKeyExists();
        
        return $this->_data[self::$_activeSection]->get($key, $defaultValue);
    }
    
    /**
     * Beállítja az adott kulcson lévő adatot (az aktív szekcióban)
     * (tömb esetén áttranszformálja kulcs-érték tárrá)
     * 
     * @param  string         a beállítandó kulcs
     * @param  mixed          a beállítandó érték
     * @return Configuration  önmagával tér vissza (láncolt hívásokhoz)
     */
    public function set($key, $value)
    {
        $this->_ensureActiveSectionKeyExists();
        
        $this->_data[self::$_activeSection]->set($key, $value);
        
        return $this;
    }
    
    private function _ensureActiveSectionKeyExists()
    {
        if (!isset($this->_data[self::$_activeSection])
        || !($this->_data[self::$_activeSection] instanceof KeyValueStorage))
        {
            $this->_data[self::$_activeSection] = new KeyValueStorage();
        }
    }
    
    /**
     * Összefésül két konfigurációs struktúrát
     * 
     * @param  Configuration  az összefésüléshez használt másik konfiguráció
     * @return Configuration  az összefésült konfiguráció 
     */
    public function merge(Configuration $other)
    {
        $mergedArray     = array_replace_recursive($this->toArray(), $other->toArray());
        $mergedStructure = new KeyValueStorage($mergedArray);
        
        $mergedConfiguration        = new Configuration();
        $mergedConfiguration->_data = $mergedStructure->_data;
        
        return $mergedConfiguration;
    }
}