<?php

namespace app\control;

use \fw\control\Context;
use \fw\control\Controller;

use \fw\log\FileTarget;
use \fw\log\Log;

class ErrorController extends Controller
{
    public function exceptionAction()
    {
        header('HTTP/1.1 500 Internal Server Error');
        
        $template               = $this->view->getActionTemplate();
        $template->debugEnabled = $this->configuration->debug->get('enabled', false);
        $template->exception    = $this->frontController->getLastException();
        
        try
        {
            $fileTarget = new FileTarget($this->configuration->log->errors->get('path', ''));
            
            $log = new Log();
            $log->addTarget($fileTarget);
            $log->error($this->frontController->getLastException()->getMessage());
        }
        catch(\Exception $ex)
        {
            // unable to log the error
        }
    }
    
    public function notFoundAction()
    {
        header('HTTP/1.1 404 Not Found');
    }
}