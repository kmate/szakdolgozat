<?php

namespace app\control;

use \fw\control\Controller;

class ErrorController extends Controller
{
    public function exceptionAction()
    {
        header('HTTP/1.1 500 Internal Server Error');
        
        
    }
    
    public function notFoundAction()
    {
        header('HTTP/1.1 404 Not Found');
        
        
    }
}