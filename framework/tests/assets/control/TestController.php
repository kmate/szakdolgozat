<?php

use \fw\Exception;
use \fw\control\Context;
use \fw\control\Controller;
use \fw\control\RouteInfo;

class TestController extends Controller
{
    public function __construct(Context $context)
    {
        parent::__construct($context);
        
        $this->view->setAutoRender(false);
    }
    
    public function contextExportAction()
    {
        var_export($this->context);
    }
    
    public function routeInfoExportAction()
    {
        var_export($this->routeInfo);
    }
    
    public function forwardToRouteInfoExportAction()
    {
        $this->frontController->forward(new RouteInfo('test', 'routeInfoExport'));
        
        echo 'This code should not be reached.';
    }
    
    public function forwardToDefaultAction()
    {
        $this->frontController->forwardToDefault();
        
        echo 'This code should not be reached.';
    }
    
    public function forwardToDefaultExternalAction()
    {
        $this->frontController->forwardToDefault(true);
        
        echo 'This code should not be reached.';
    }
    
    public function forwardToMissingAction()
    {
        $this->frontController->forward(new RouteInfo('test', 'missing'));
        
        echo 'This code should not be reached.';
    }
    
    public function exceptionAction()
    {
        throw new Exception();
    }
    
    public function emptyAction()
    {
    }
}