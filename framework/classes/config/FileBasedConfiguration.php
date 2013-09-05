<?php

namespace fw\config;

/**
 * Fájl alapú konfigurációk ősosztálya
 * 
 * @author Karácsony Máté
 */
abstract class FileBasedConfiguration extends Configuration
{
    /**
     * Konfigurációs struktúrába transzformálja az útvonalával megadott fájlt
     * 
     * @param  string     a betöltendő fájl elérési útvonala
     * @throws Exception  a fájl nem olvasható vagy értelmezési hiba történt
     */
    public function __construct($filePath)
    {
        $this->_checkFileIsReadable($filePath);
        
        $filePath = realpath($filePath);
        
        if (false !== ($result = $this->_parseFile($filePath)))
        {
            parent::__construct($this->_parseIntoArray($result), true);
        }
        else
        {
            $this->_throwParseException($filePath, $result);
        }
    }
    
    /**
     * Értelmezi a kiválasztott fájlt
     * 
     * @param  string     értelmezésre küldött fájl elérési útvonala
     * @return mixed      az értelmező nyers eredménye
     * @throws Exception  értelmezési hiba történt a fájl beolvasása során
     */
    abstract protected function _parseFile($filePath);
    
    /**
     * Asszociatív tömbbé transzformálja az értelmező eredményét
     * 
     * @param  mixed  az értelmező nyers eredménye
     * @return array  tömbbé transzformált eredmény
     */
    abstract protected function _parseIntoArray($parserResult);
    
    private function _checkFileIsReadable($filePath)
    {
        if (!is_readable($filePath))
        {
            throw new Exception(
                'Unable to load configuration file: \'' . $filePath . '\'',
                Exception::UNABLE_TO_READ_FILE
            );
        }
    }
    
    private function _throwParseException($filePath, $parseResult)
    {
        throw new Exception(
            'Unable to parse configuration file: \'' . $filePath . '\'',
            Exception::UNABLE_TO_PARSE_FILE
        );
    }
}