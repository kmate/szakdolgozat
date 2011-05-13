<?php

use \fw\control\Context;
use \fw\control\hooks\ControllerHook;

class RouteInfoHook implements ControllerHook
{
    public function execute(Context $context)
    {
        var_export($context->getRouteInfo());
    }
}