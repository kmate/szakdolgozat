<?php

namespace fw\log;

use \fw\Utils;

/**
 * Naplózási célok ősosztálya
 * 
 * @author Karácsony Máté
 */
abstract class LogTarget
{
    const DEFAULT_FORMAT = '%t - [%l] %s: %m (%h)';
    
    /**
     * Új naplózási célt hoz létre a kívánt formátum-sztringgel
     * 
     * @param string  a naplósorok formátum-sztringje
     */
    public function __construct($formatString = self::DEFAULT_FORMAT)
    {
        $this->_formatString = $formatString;
    }
    
    /**
     * Kiírja a megadott szintű, forrású, üzenetű bejegyzést a naplózási célba
     * 
     * @param  string  bejegyzés szintje
     * @param  string  bejegyzés forrása
     * @param  string  bejegyzés üzenete
     * @return void
     */
    abstract public function write($level, $source = Log::UNKNOWN_SOURCE, $message = '-');
    
    /**
     * Naplósort formáz a megadott szintű, forrású, üzenetű bejegyzésből
     * 
     * @param  string  bejegyzés szintje
     * @param  string  bejegyzés forrása
     * @param  string  bejegyzés üzenete
     * @return string  formázott naplósor
     */
    public function format($level, $source = Log::UNKNOWN_SOURCE, $message = '-')
    {
        $replacements = array(
            '%l' => $level,
            '%s' => $source,
            '%m' => $message,
            '%t' => date('d/M/Y:H:i:s O'),
            '%h' => Utils::getClientIp()
        );
        
        return strtr($this->_formatString, $replacements);
    }
}