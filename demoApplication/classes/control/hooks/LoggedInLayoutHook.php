<?php

namespace app\control\hooks;

use \fw\SessionManager;
use \fw\control\Context;

class LoggedInLayoutHook implements \fw\control\hooks\ControllerHook
{
    public function execute(Context $context)
    {
        if (isset($_SESSION['user']))
        {
            $context->view->getLayoutTemplate()->user = $_SESSION['user'];
        }
    }
}