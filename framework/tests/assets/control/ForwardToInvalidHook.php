<?php

use \fw\control\Context;
use \fw\control\RouteInfo;
use \fw\control\hooks\ControllerHook;

class ForwardToInvalidHook implements ControllerHook
{
    public function execute(Context $context)
    {
        $context->frontController->forward(new RouteInfo('invalid', ''));
        
        echo 'This code should not be reached.';
    }
}