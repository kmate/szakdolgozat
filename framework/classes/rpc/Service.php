<?php

namespace fw\rpc;

use \fw\config\Configuration;

/**
 * Távoli eljáráshívási szolgáltatás interfész
 * 
 * @author Karácsony Máté
 */
interface Service
{
    /**
     * Konfiguráció lekérdezése
     * 
     * @return Configuration
     */
    function getConfiguration();
    
    /**
     * Konfiguráció beállítása
     * 
     * @param  Configuration  új konfiguráció
     * @return void
     */
    function setConfiguration(Configuration $config);
}