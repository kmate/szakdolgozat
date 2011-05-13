<?php

namespace app\control;

use \fw\control\RouteInfo;
use \fw\control\Controller;

use fw\rpc\json\Server;

class RpcController extends Controller
{
    public function indexAction()
    {
        $this->frontController->forwardExternal(new RouteInfo('task', 'list'));
    }
    
    public function jsonAction()
    {
        $this->view->setAutoRender(false);
        
        $server = new Server($this->configuration);
        echo $server->handle(file_get_contents('php://input'));
    }
}