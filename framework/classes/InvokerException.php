<?php

namespace fw;

/**
 * Az önelemzésen keresztüli metódushívás által dobott kivételek osztálya
 * 
 * @author Karácsony Máté
 */
class InvokerException extends Exception
{
    const MISSING_CLASS          = 1;
    const INVALID_IMPLEMENTATION = 2;
    const INVALID_SUBCLASS       = 3;
    const MISSING_METHOD         = 4;
    const INVALID_PARAMETERS     = 5;
}
