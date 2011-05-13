<?php

namespace fw\tests\view;

use \fw\view\Template;
use \PHPUnit_Extensions_OutputTestCase;

if (!defined('VIEW_TEST_ASSETS_PATH'))
{
    define('VIEW_TEST_ASSETS_PATH', TEST_ASSETS_PATH . DIRECTORY_SEPARATOR . 'view');
}

class TemplateTest extends PHPUnit_Extensions_OutputTestCase
{
    public function testEvaluateWritesOutput()
    {
        $template = new Template('render_test', VIEW_TEST_ASSETS_PATH);
        $template->evaluate();
        
        $this->expectOutputString('test');
    }
    
    public function testEvaluateReturnsOutput()
    {
        $template = new Template('render_test', VIEW_TEST_ASSETS_PATH);
        $output   = $template->evaluate(true);
        
        $this->assertEquals('test', $output);
    }
    
    /**
     * @expectedException     \fw\view\TemplateException
     * @expectedExceptionCode 1 (TemplateException::MISSING_TEMPLATE_FILE)
     */
    public function testEvaluateThrowsExceptionOnMissingTemplateFile()
    {
        $template = new Template('missing', VIEW_TEST_ASSETS_PATH);
        $template->evaluate();
    }
    
    public function testCanStoreVariablesForSubstitution()
    {
        $template           = new Template('render_test', VIEW_TEST_ASSETS_PATH);
        $template->variable = 'value';
        
        $this->assertEquals('value', $template->variable);
    }
    
    public function testVariableSubstitution()
    {
        $template           = new Template('substitution_test', VIEW_TEST_ASSETS_PATH);
        $template->variable = 'value';
        
        $this->assertEquals('value', $template->evaluate(true));
    }
}