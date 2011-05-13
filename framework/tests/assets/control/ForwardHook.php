<?php

use \fw\control\Context;
use \fw\control\RouteInfo;
use \fw\control\hooks\ControllerHook;

class ForwardHook implements ControllerHook
{
    public function execute(Context $context)
    {
        $context->frontController->forward(new RouteInfo('test', 'route-info-export'));
        
        echo 'This code should not be reached.';
    }
}