<?php

namespace fw\tests;

use \fw\ClassLoader;
use \PHPUnit_Framework_TestCase;

class ClassLoaderTest extends PHPUnit_Framework_TestCase
{
    private $_classLoader;
    
    private function setUpClassLoader()
    {
        $this->_classLoader = new ClassLoader();
    }
    
    private function setUpAutoClassLoader()
    {
        $this->setUpAutoClassLoaderWithClassPathAndDefaultNamespace('', '');
    }
    
    private function setUpAutoClassLoaderWithClassPathAndDefaultNamespace(
        $classPath,
        $defaultNamespace)
    {
        $this->_classLoader = new ClassLoader($classPath, $defaultNamespace, true);
    }
    
    public function tearDown()
    {
        $this->_classLoader->unregister();
        unset($this->_classLoader);
    }
    
    public function testRegister()
    {
        $this->setUpClassLoader();
        $countBeforeRegister = count(spl_autoload_functions());
        
        $this->_classLoader->register();
        $countAfterRegister = count(spl_autoload_functions());
        $this->assertEquals($countBeforeRegister + 1, $countAfterRegister);
    }
    
    public function testUnregister()
    {
        $this->setUpAutoClassLoader();
        $countAfterRegister = count(spl_autoload_functions());
        
        $this->_classLoader->unregister();
        $countAfterUnregister = count(spl_autoload_functions());
        $this->assertEquals($countAfterRegister - 1, $countAfterUnregister);
    }
    
    public function testAutoRegister()
    {
        $countBeforeRegister = count(spl_autoload_functions());
        
        $this->setUpAutoClassLoader();
        
        $this->assertEquals($countBeforeRegister + 1, count(spl_autoload_functions()));
    }
    
    private function assertClassLoaded($fullQualifiedClassName)
    {
        $this->assertTrue(
            !class_exists($fullQualifiedClassName, false) &&
             class_exists($fullQualifiedClassName)
        );
    }
    
    public function testLoadExistingClassInGlobalNamespace()
    {
        $this->setUpAutoClassLoaderWithClassPathAndDefaultNamespace(TEST_ASSETS_PATH, '');
        
        $this->assertClassLoaded('\\AClass');
    }
    
    public function testLoadExistingClassInGlobalNamespaceAsSlash()
    {
        $this->setUpAutoClassLoaderWithClassPathAndDefaultNamespace(TEST_ASSETS_PATH, '\\');
        
        $this->assertClassLoaded('\\BClass');
    }
    
    public function testLoadExistingClassInDefaultNamespace()
    {
        $this->setUpAutoClassLoaderWithClassPathAndDefaultNamespace(TEST_ASSETS_PATH, 'tests\\assets');
        
        $this->assertClassLoaded('\\tests\\assets\\CClass');
    }
    
    public function testLoadExistingClassInDefaultNamespace2()
    {
        $this->setUpAutoClassLoaderWithClassPathAndDefaultNamespace(__DIR__, '');
        
        $this->assertClassLoaded('\\assets\\DClass');
    }
    
    public function testClassOutsideDefaultNamespaceWillNotBeLoaded()
    {
        $this->setUpAutoClassLoaderWithClassPathAndDefaultNamespace(TEST_ASSETS_PATH, 'tests\\assets');
        
        $this->assertFalse(class_exists('\\it\\will\\not\\load\\this\\Class'));
    }
}
