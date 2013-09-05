<?php

namespace fw\log;

/**
 * Fájlba író naplózási cél
 * 
 * @author Karácsony Máté
 */
class FileTarget extends LogTarget
{
    private $_handle;
    
    /**
     * Megnyitja az útvonalával meghatározott naplófájlt írásra és beállítja a formátum-sztringet
     * 
     * @param  string         a naplófájl elérési útvonala
     * @param  string         a naplósorok formátum-sztringje
     * @throws FileException  a megadott fájl nem nyitható meg írásra
     */
    public function __construct($filePath, $formatString = LogTarget::DEFAULT_FORMAT)
    {
        if (false === ($this->_handle = @fopen($filePath, 'a')))
        {
            throw new FileException(
                'Unable to open log file for writing: \'' . $filePath . '\'',
                FileException::UNABLE_TO_OPEN
            );
        }
        
        parent::__construct($formatString);
    }
    
    /**
     * Lezárja a konstruktorban megnyitott fájlt
     */
    public function __destruct()
    {
        if ($this->_handle)
        {
            fclose($this->_handle);
        }
    }
    
    /**
     * Kiírja a megadott szintű, forrású, üzenetű bejegyzést a megadott fájlba
     * 
     * @param  string  bejegyzés szintje
     * @param  string  bejegyzés forrása
     * @param  string  bejegyzés üzenete
     * @return void
     */
    public function write($level, $source = '', $message = '')
    {
        fwrite($this->_handle, $this->format($level, $source, $message) . PHP_EOL);
    }
}