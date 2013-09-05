<?php

namespace fw\rpc;

/**
 * Távoli eljáráshívási kérés interfész
 * 
 * @author Karácsony Máté
 */
interface Request
{
    /**
     * Válasz dekódolása a átviteli formátumról
     * 
     * @param  string  nyers átviteli adat
     * @return void
     */
    function decode($rawData);
    
    /**
     * Kérés helyességének ellenőrzése
     * 
     * @return bool
     */
    function isValid();
}