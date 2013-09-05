<?php

namespace fw\log;

/**
 * Naplózó osztályok kivételeinek ősosztálya
 * 
 * @author Karácsony Máté
 */
class Exception extends \fw\Exception
{
    const INVALID_LOG_LEVEL = 1;
}
