<?php

namespace fw\control\hooks;

use \fw\control\Context;

interface ControllerHook
{
    function execute(Context $context);
}