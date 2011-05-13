<?php

use \fw\control\Context;
use \fw\control\Controller;

class ErrorController extends Controller
{
    public function __construct(Context $context)
    {
        parent::__construct($context);
        
        $this->view->setAutoRender(false);
    }
    
    public function exceptionAction()
    {
        echo $this->frontController->getLastException();
    }
    
    public function notFoundAction()
    {
        echo 'notFoundAction';
    }
}