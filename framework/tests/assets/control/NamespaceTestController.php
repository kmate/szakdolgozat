<?php

namespace control;

use \fw\control\Context;
use \fw\control\Controller;

class NamespaceTestController extends Controller
{
    public function __construct(Context $context)
    {
        parent::__construct($context);
        
        $this->view->setAutoRender(false);
    }
    
    public function namespaceEchoAction()
    {
        echo __NAMESPACE__;
    }
}