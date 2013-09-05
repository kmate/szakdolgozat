<?php

namespace fw\control\hooks;

use \fw\control\Context;

/**
 * Vezérlés-kiegészítők interfésze
 * 
 * @author Karácsony Máté
 */
interface ControllerHook
{
    /**
     * Végrehajtás
     * 
     * @param  Context  vezérlési környezet
     * @return void
     */
    function execute(Context $context);
}