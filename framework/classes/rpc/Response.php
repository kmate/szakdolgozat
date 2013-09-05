<?php

namespace fw\rpc;

/**
 * Távoli eljáráshívási válasz interfész
 * 
 * @author Karácsony Máté
 */
interface Response
{
    /**
     * Válasz kódolása a átviteli formátumra
     * 
     * @return string
     */
    function encode();
}