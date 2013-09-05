<?php

namespace fw\log;

/**
 * Fájl-alapú naplózás kivétel osztálya
 * 
 * @author Karácsony Máté
 */
class FileException extends Exception
{
    const UNABLE_TO_OPEN = 1;
}
