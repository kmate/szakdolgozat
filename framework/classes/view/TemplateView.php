<?php

namespace fw\view;

use \fw\config\Configuration;
use \fw\control\RouteInfo;

/**
 * Sablon alapú nézet
 * 
 * @author Karácsony Máté
 */
class TemplateView extends View
{
    const DEFAULT_LAYOUT_TEMPLATE = 'layout';
    
    private $_config;
    
    private $_templateDirectory;
    
    private $_layoutTemplate;
    private $_actionTemplate;
    
    /**
     * Előkészíti a sablon-nézetet, beállítja a sablonok könyvtárát a konfiguráció alapján
     * (a view.template_directory beállítást felhasználva)
     *
     * @param Configuration konfiguráció
     */
    public function __construct(Configuration $config = null)
    {
        if (null == $config)
        {
            $config = new Configuration();
        }
        
        $this->_config            = $config;
        $this->_templateDirectory = $config->view->get('template_directory', __DIR__);
    }
    
    /**
     * Lekérdezi az aktív elrendezési sablont
     * 
     * @return Template
     */
    public function getLayoutTemplate()
    {
        return $this->_layoutTemplate;
    }
    
    /**
     * Lekérdezi az aktív vezérlő-akció sablont
     * 
     * @return Template
     */
    public function getActionTemplate()
    {
        return $this->_actionTemplate;
    }
    
    /**
     * Beállítja az aktív elrendezési és vezérlő-akció sablonokat egy útvonal információ alapján
     * 
     * @param  RouteInfo  útvonal információ
     * @return void
     */
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
    
    /**
     * Beállítja az aktív elrendezési sablont
     * 
     * @param  Template  az új elrendezési sablon
     * @return void
     */
    public function setLayoutTemplate(Template $template)
    {
        $this->_layoutTemplate = $template;
    }
    
    /**
     * Beállítja az aktív vezérlő-akció sablont
     * 
     * @param  Template  az új vezérlő-akció sablon
     * @return void
     */
    public function setActionTemplate(Template $template)
    {
        $this->_actionTemplate = $template;
    }
    
    /**
     * Új sablont hoz létre a konfigurációból olvasott könyvtárból a sablon neve alpján
     * 
     * @param  string    az új sablon neve
     * @return Template
     */
    public function createTemplate($templateName)
    {
        return new Template($templateName, $this->_templateDirectory);
    }
    
    /**
     * Kinyeri a vezérlő-akció sablon nevét egy útvonal információból
     * 
     * @param  RouteInfo  útvonal információ
     * @return string     a sablon neve
     */
    public function parseActionTemplateName(RouteInfo $routeInfo)
    {
        return $routeInfo->getControllerName() . DIRECTORY_SEPARATOR . $routeInfo->getActionName();
    }
    
    /**
     * Kinyeri az elrendezés sablon nevét egy útvonal információból
     * 
     * @param  RouteInfo  útvonal információ
     * @return string     a sablon neve
     */
    public function parseLayoutTemplateName(RouteInfo $routeInfo)
    {
        $defaultLayoutName = $this->_config->view->get('default_layout', self::DEFAULT_LAYOUT_TEMPLATE);
        
        return $this->_config->view->layout
            ->get($routeInfo->getControllerName())
            ->get($routeInfo->getActionName(), $defaultLayoutName);
    }
    
    /**
     * Nézet-sablonok kiértékelése
     * 
     * @param  bool  visszatérés a kiértékelés eredményével (hamis érték esetén a kimenetre írja)
     * @return string
     */
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