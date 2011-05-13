<?php

namespace fw\view;

use \fw\config\Configuration;
use \fw\control\RouteInfo;

class TemplateView extends View
{
    const DEFAULT_LAYOUT_TEMPLATE = 'layout';
    
    private $_config;
    
    private $_templateDirectory;
    
    private $_layoutTemplate;
    private $_actionTemplate;
    
    public function __construct(Configuration $config = null)
    {
        if (null == $config)
        {
            $config = new Configuration();
        }
        
        $this->_config            = $config;
        $this->_templateDirectory = $config->view->get('template_directory', __DIR__);
    }
    
    public function getLayoutTemplate()
    {
        return $this->_layoutTemplate;
    }
    
    public function getActionTemplate()
    {
        return $this->_actionTemplate;
    }
    
    public function setTemplatesByRouteInfo(RouteInfo $routeInfo)
    {
        $layoutTemplateName = $this->parseLayoutTemplateName($routeInfo);
        $actionTemplateName = $this->parseActionTemplateName($routeInfo);
        
        if (null == $this->getLayoutTemplate())
        {
            $this->setLayoutTemplate($this->createTemplate($layoutTemplateName));
        }
        $this->setActionTemplate($this->createTemplate($actionTemplateName));
    }
    
    public function setLayoutTemplate(Template $template)
    {
        $this->_layoutTemplate = $template;
    }
    
    public function setActionTemplate(Template $template)
    {
        $this->_actionTemplate = $template;
    }
    
    public function createTemplate($templateName)
    {
        return new Template($templateName, $this->_templateDirectory);
    }
    
    public function parseActionTemplateName(RouteInfo $routeInfo)
    {
        return $routeInfo->getControllerName() . DIRECTORY_SEPARATOR . $routeInfo->getActionName();
    }
    
    public function parseLayoutTemplateName(RouteInfo $routeInfo)
    {
        $defaultLayoutName = $this->_config->view->get('default_layout', self::DEFAULT_LAYOUT_TEMPLATE);
        
        return $this->_config->view->layout
            ->get($routeInfo->getControllerName())
            ->get($routeInfo->getActionName(), $defaultLayoutName);
    }
    
    protected function _render($returnContents = false)
    {
        foreach ($this->_config->view->layout_variables->toArray() as $variableName => $value)
        {
            $this->_layoutTemplate->{$variableName} = $value;
        }
        
        $this->_actionTemplate->view           = $this;
        $this->_layoutTemplate->view           = $this;
        $this->_layoutTemplate->layoutContents = $this->_actionTemplate->evaluate(true);
        return $this->_layoutTemplate->evaluate($returnContents);
    }
}