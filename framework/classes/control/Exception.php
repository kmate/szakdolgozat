<?php

namespace fw\control;

class Exception extends \fw\Exception
{
    const MISSING_CONTROLLER_CLASS       = 1;
    const INVALID_CONTROLLER_CLASS       = 2;
    const INVALID_ACTION_METHOD          = 3;
    const MISSING_HOOK_CLASS             = 4;
    const INVALID_HOOK_CLASS             = 5;
    
    const STOPPED_BY_FORWARD             = 10;
}
