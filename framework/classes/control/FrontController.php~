<?php

namespace fw\control;

use \fw\ClassLoader;
use \fw\Invoker;
use \fw\InvokerException;
use \fw\config\Configuration;
use \fw\view\ViewUtils;

class FrontController
{
    const DEFAULT_CLASSPATH = __DIR__;
    const DEFAULT_NAMESPACE = '';
    
    const CONTROLLER_SUFFIX = 'Controller';
    const ACTION_SUFFIX     = 'Action';
    
    protected $_classLoader;
    protected $_namespace;
    protected $_config;
    protected $_router;
    protected $_context;
    
    protected $_preHooks;
    protected $_postHooks;
    
    protected $_lastException;
    protected $_handlingNotFound;
    
    public function __construct(Configuration $config = null)
    {
        if (null == $config)
        {
            $config = new Configuration();
        }
        
        $this->_config = $config;
        
        $classPath        = $this->_config->controller->get('classpath', self::DEFAULT_CLASSPATH);
        $this->_namespace = $this->_config->controller->get('namespace', self::DEFAULT_NAMESPACE);
        $this->_preHooks  = $this->_config->controller->get('pre_hooks')->toArray();
        $this->_postHooks = $this->_config->controller->get('post_hooks')->toArray();
        
        $this->_classLoader = new ClassLoader($classPath, $this->_namespace);
        $this->_classLoader->register();
    }
    
    public function __destruct()
    {
        $this->_classLoader->unregister();
        unset($this->_classLoader);
    }
    
    public function getConfiguration()
    {
        return $this->_config;
    }
    
    public function getLastException()
    {
        return $this->_lastException;
    }
    
    public function getRouter()
    {
        if (null == $this->_router)
        {
            $this->setRouter(new UrlRouter($this->_config));
        }
        
        return $this->_router;
    }
    
    public function setRouter(Router $router)
    {
        $this->_router = $router;
        
        ViewUtils::setRouter($router);
    }
    
    public function getContext()
    {
        if (null == $this->_context)
        {
            $this->setContext(new HttpContext());
        }
        
        return $this->_context;
    }
    
    public function setContext(Context $context)
    {
        $this->_context = $context;
        $this->_context->setFrontController($this);
    }
    
    public function getControllerClassName(RouteInfo $routeInfo)
    {
        $parsedNamePart = preg_replace(
            '/\\-([a-z])/e', 'strtoupper("\\1")',
            ucfirst(strtolower($routeInfo->getControllerName()))
        );
        
        $fullQualifiedClassName = $this->_namespace . '\\' . $parsedNamePart . self::CONTROLLER_SUFFIX;
        
        if ('\\' !== substr($fullQualifiedClassName, 0, 1))
        {
            $fullQualifiedClassName = '\\' . $fullQualifiedClassName;
        }
        
        return $fullQualifiedClassName;
    }
    
    public function getActionMethodName(RouteInfo $routeInfo)
    {
        $parsedNamePart = preg_replace(
            '/\\-([a-z])/e', 'strtoupper("\\1")',
            strtolower($routeInfo->getActionName())
        );
        
        return $parsedNamePart . self::ACTION_SUFFIX;
    }
    
    public function dispatch($route = null)
    {
        $this->_handlingNotFound = false;
        
        $routeInfo = $this->getRouter()->parseRoute($route);
        $this->getContext()->setRouteInfo($routeInfo);
        
        $continueDispatch = $this->_runHooks($this->_preHooks);
        
        if ($continueDispatch)
        {
            $this->_dispatchInternal();
        }
        
        $this->_runHooks($this->_postHooks);
    }
    
    public function forward(RouteInfo $routeInfo)
    {
        $this->getContext()->setRouteInfo($routeInfo);
        
        $this->_dispatchInternal();
        
        throw new Exception('', Exception::STOPPED_BY_FORWARD);
    }
    
    public function forwardExternal(RouteInfo $routeInfo)
    {
        $this->getRouter()->redirect($routeInfo);
    }
    
    public function forwardToDefault($external = false)
    {
        $routeInfo = $this->getRouter()->parseRoute('');
        
        if ($external)
        {
            $this->forwardExternal($routeInfo);
        }
        else
        {
            $this->forward($routeInfo);
        }
    }
    
    private function _runHooks($hooks)
    {
        foreach ($hooks as $hookClassName)
        {
            try
            {
                $invoker = new Invoker($hookClassName);
                $invoker->checkInterface('\\fw\\control\\hooks\\ControllerHook');
                $invoker->invoke('execute', array($this->getContext()));
            }
            catch (Exception $ex)
            {
                $this->_checkStopException($ex);
                
                return false;
            }
            catch (InvokerException $ex)
            {
                if (InvokerException::MISSING_CLASS == $ex->getCode())
                {
                    throw new Exception(
                        'Missing hook class: \'' . $hookClassName . '\'',
                        Exception::MISSING_HOOK_CLASS,
                        $ex
                    );
                }
                else
                {
                    throw new Exception(
                        'Invalid hook class: \'' . $hookClassName . '\'',
                        Exception::INVALID_HOOK_CLASS,
                        $ex
                    );
                }
            }
        }
        
        return true;
    }
    
    private function _dispatchInternal()
    {
        $controllerClassName = $this->getControllerClassName($this->getContext()->getRouteInfo());
        $actionMethodName    = $this->getActionMethodName($this->getContext()->getRouteInfo());
        
        try
        {
            $invoker = new Invoker($controllerClassName, array($this->getContext()));
            $invoker->checkParentClass('\\fw\\control\\Controller');
            $invoker->invoke($actionMethodName);
        }
        catch (Exception $ex)
        {
            $this->_checkStopException($ex);
            
            return;
        }
        catch(InvokerException $ex)
        {
            $this->_handleDispatchInvokerException($ex, $controllerClassName, $actionMethodName);
            
            return;
        }
        catch(\Exception $ex)
        {
            $this->_lastException = $ex;
            
            $routeInfo = new RouteInfo(
                $this->getRouter()->getErrorController(),
                $this->getRouter()->getExceptionAction()
            );
            $this->getContext()->setRouteInfo($routeInfo);
            
            $this->_dispatchInternal();
            
            return;
        }
        
        $this->getContext()->view->runAutoRenderer();
    }
    
    private function _checkStopException(Exception $ex)
    {
        if (Exception::STOPPED_BY_FORWARD != $ex->getCode())
        {
            throw $ex;
        }
    }
    
    private function _handleDispatchInvokerException(InvokerException $ex, $controllerClassName, $actionMethodName)
    {
        if (InvokerException::INVALID_SUBCLASS == $ex->getCode())
        {
            throw new Exception(
                'Invalid controller class: \'' . $controllerClassName . '\'',
                Exception::INVALID_CONTROLLER_CLASS,
                $ex
            );
        }
        else
        {
            $this->_handleNotFound($ex, $controllerClassName, $actionMethodName);
        }
    }
    
    private function _handleNotFound(InvokerException $ex, $controllerClassName, $actionMethodName)
    {
        if (!$this->_handlingNotFound)
        {
            $this->_handlingNotFound = true;
            
            $routeInfo = new RouteInfo(
                $this->getRouter()->getErrorController(),
                $this->getRouter()->getNotFoundAction()
            );
            $this->getContext()->setRouteInfo($routeInfo);
            
            $this->_dispatchInternal();
        }
        else
        {
            $this->_rethrowByInvokerException($ex, $controllerClassName, $actionMethodName);
        }
    }
    
    private function _rethrowByInvokerException(InvokerException $ex, $controllerClassName, $actionMethodName)
    {
        if (InvokerException::MISSING_CLASS == $ex->getCode())
        {
            throw new Exception(
                'Missing controller class: \'' . $controllerClassName . '\'',
                Exception::MISSING_CONTROLLER_CLASS,
                $ex
            );
        }
        else
        {
            throw new Exception(
                'Invalid action method name: \'' . $actionMethodName . '\'',
                Exception::INVALID_ACTION_METHOD,
                $ex
            );
        }
    }
}