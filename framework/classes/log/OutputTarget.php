<?php

namespace fw\log;

/**
 * Standard kimenetre író naplózási cél
 * 
 * @author Karácsony Máté
 */
class OutputTarget extends LogTarget
{
    /**
     * Kiírja a megadott szintű, forrású, üzenetű bejegyzést a standart kimenetre
     * 
     * @param  string  bejegyzés szintje
     * @param  string  bejegyzés forrása
     * @param  string  bejegyzés üzenete
     * @return void
     */
    public function write($level, $source = '', $message = '')
    {
        echo $this->format($level, $source, $message) . PHP_EOL;
    }
}