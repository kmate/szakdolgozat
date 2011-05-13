<?php

namespace app\control;

use \app\model\DatabaseModelException;
use \app\model\User;
use \app\model\UserModel;

use \fw\KeyValueStorage;
use \fw\SessionManager;

use \fw\control\Context;
use \fw\control\Controller;

use \fw\input\EmailValidator;
use \fw\input\RegexpValidator;
use \fw\input\StringValidator;
use \fw\input\StringCompareValidator;
use \fw\input\ValidatorGroup;

use \fw\log\FileTarget;
use \fw\log\Log;

class UserController extends Controller
{
    private $_log;
    
    private function _getLog()
    {
        if (null == $this->_log)
        {
            $fileTarget = new FileTarget($this->configuration->log->users->get('path', ''));
            
            $this->_log = new Log($this->configuration->debug->get('enabled', false));
            $this->_log->addTarget($fileTarget);
        }
        
        return $this->_log;
    }
    
    public function loginAction()
    {
        if (isset($_SESSION['user']))
        {
            $this->frontController->forwardToDefault(true);
        }
        
        $template = $this->view->getActionTemplate();
        $template->showLoginError   = false;
        $template->validationErrors = new KeyValueStorage();
        $template->userName         = '';
        
        $parameters = $this->postParameters;
        
        if ($parameters->has('login'))
        {
            $validator = ValidatorGroup::build();
            $validator->addValidator(StringValidator::build()->required(), 'userName', '')
                      ->addValidator(StringValidator::build()->required(), 'password', '');
            
            if (!$validator->validate($parameters))
            {
                $template->validationErrors = $validator->getLastErrors();
                $template->userName         = $parameters->userName;
            }
            else
            {
                $userModel = new UserModel($this->configuration);
                $userModel->connect();
                
                $user = $userModel->getUserByName($parameters->userName);
                
                if (null != $user &&
                    $userModel->validatePassword($user->password, $parameters->password))
                {
                    SessionManager::login();
                    
                    $user->password = null;
                    
                    $_SESSION['user'] = $user;
                    
                    $this->_getLog()->info('Sikeres bejelentkezés, felhasználónév: ' . $user->name);
                    
                    $this->frontController->forwardToDefault(true);
                }
                else
                {
                    $this->_getLog()->warning('Sikertelen bejelentkezés, felhasználónév: ' . $parameters->userName);
                    
                    $template->showLoginError = true;
                    $template->userName       = $parameters->userName;
                }
            }
        }
    }
    
    public function logoutAction()
    {
        $this->_getLog()->info('Sikeres kijelentkezés, felhasználónév: ' . $_SESSION['user']->name);
        
        SessionManager::destroy();
        
        $this->frontController->forwardToDefault(true);
    }
    
    public function registerAction()
    {
        $template = $this->view->getActionTemplate();
        $template->showUserNameError = false;
        $template->showEmailError    = false;
        $template->validationErrors  = new KeyValueStorage();
        $template->parameters        = new KeyValueStorage();
        
        $parameters = $this->postParameters;
        
        if ($parameters->has('register'))
        {
            $validator = ValidatorGroup::build();
            $validator->addValidator(
                            RegexpValidator::build()
                                ->pattern('/^[a-zA-Z0-9]+$/')
                                ->minLength(3)
                                ->maxLength(255)
                                ->required(),
                            'userName',
                            ''
                        )
                      ->addValidator(StringValidator::build()->maxLength(255)->required(), 'fullName', '')
                      ->addValidator(EmailValidator::build()->maxLength(255)->required(), 'email', '')
                      ->addValidator(
                            RegexpValidator::build()
                                ->pattern('/^[\pL0-9\!\?\;\:\.\_\-\+\=\%\&\#]+$/ui')
                                ->minLength(6)
                                ->maxLength(32)
                                ->required(),
                            'password',
                            ''
                      )
                      ->addValidator(
                            StringCompareValidator::build()->compareTo($parameters->password)->required(),
                            'password2',
                            ''
                      );
            
            $template->parameters = $parameters;
            
            if (!$validator->validate($parameters))
            {
                $template->validationErrors = $validator->getLastErrors();
            }
            else
            {
                $userModel = new UserModel($this->configuration);
                $userModel->connect();
                
                $newUser = User::create(
                    null,
                    $parameters->get('userName'),
                    $parameters->get('fullName'),
                    $parameters->get('email'),
                    $userModel->hashPassword($parameters->get('password'))
                );
                
                try
                {
                    $userModel->addUser($newUser);
                    
                    $this->_getLog()->info('Új felhasználó hozzáadása sikeres, felhasználónév: ' . $newUser->name);
                    $this->_getLog()->debug('Új felhasználó azonosítója: ' . $newUser->id);
                    $this->_getLog()->debug('Új felhasználó kódolt jelszava: ' . $newUser->password);
                    
                    $this->view->setActionTemplate($this->view->createTemplate('user/register-successful'));
                }
                catch(DatabaseModelException $ex)
                {
                    if (DatabaseModelException::UNIQUE_CONSTRAINT_FAIL == $ex->getCode())
                    {
                        if (User::FIELD_NAME == $ex->fieldName)
                        {
                            $template->showUserNameError = true;
                        }
                        else if (User::FIELD_EMAIL == $ex->fieldName)
                        {
                            $template->showEmailError = true;
                        }
                    }
                    else
                    {
                        throw $ex;
                    }
                }
            }
        }
    }
    
    public function viewAction()
    {
        $userModel = new UserModel($this->configuration);
        $userModel->connect();
        
        $userId = (int)$this->routeParameters->get('userId', 0);
        $user   = $userModel->getUserById($userId);
        
        if (null == $user)
        {
            $this->frontController->forwardToDefault();
        }
        else
        {
            $this->view->getActionTemplate()->user = $user;
        }
    }
}