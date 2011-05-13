<?php

namespace app\control\hooks;

use \app\model\UserModel;
use \fw\SessionManager;
use \fw\control\Context;

class AuthenticationHook extends \fw\control\hooks\AuthenticationHook
{
    public function validateSession(Context $context)
    {
        if (!SessionManager::isValid() || !isset($_SESSION['user']))
        {
            return false;
        }
        else
        {
            $userModel = new UserModel($context->configuration);
            $userModel->connect();
            
            if (null == $userModel->getUserById($_SESSION['user']->id))
            {
                unset($_SESSION['user']);
                
                return false;
            }
        }
        
        return true;
    }
}