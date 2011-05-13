<?php

namespace fw\tests;

use \fw\SessionManager;
use \fw\Utils;
use \PHPUnit_Framework_TestCase;

/**
 * @outputBuffering enabled
 */
class SessionManagerTest extends PHPUnit_Framework_TestCase
{
    public function testStartWithPredefinedName()
    {
        $newSessionName = 'test';
        
        SessionManager::start($newSessionName);
        
        $this->assertEquals(session_name(), $newSessionName);
    }
    
    /**
     * @depends testStartWithPredefinedName
     */
    public function testValidationFailsWithoutLogin()
    {
        $this->assertFalse(SessionManager::isValid());
    }
    
    /**
     * @depends testValidationFailsWithoutLogin
     */
    public function testCreateLoggedInSession()
    {
        $oldSessionId = session_id();
        
        SessionManager::login();
        
        $this->assertNotEquals($oldSessionId, session_id());
        $this->assertTrue($_SESSION[SessionManager::VAR_LOGGED_IN]);
        $this->assertEquals(Utils::getClientIp(),  $_SESSION[SessionManager::VAR_CLIENT_IP]);
        $this->assertEquals(Utils::getUserAgent(), $_SESSION[SessionManager::VAR_USER_AGENT]);
    }
    
    /**
     * @depends testCreateLoggedInSession
     */
    public function testValidationSucceedsWithSameClient()
    {
        $this->assertTrue(SessionManager::isValid());
    }
    
    /**
     * @depends testValidationSucceedsWithSameClient
     */
    public function testValidationFailsWithDifferentClientIp()
    {
        $_SERVER['REMOTE_ADDR'] = '0.0.0.0';
        
        $this->assertFalse(SessionManager::isValid());
        
        unset($_SERVER['REMOTE_ADDR']);
    }
    
    /**
     * @depends testValidationFailsWithDifferentClientIp
     */
    public function testValidationFailsWithDifferentUserAgent()
    {
        $_SERVER['HTTP_USER_AGENT'] = 'test-agent';
        
        $this->assertFalse(SessionManager::isValid());
        
        unset($_SERVER['HTTP_USER_AGENT']);
    }
    
    /**
     * @depends testValidationFailsWithDifferentUserAgent
     */
    public function testValidateWithDifferentUserAgent()
    {
    /*
        $this->assertFalse($_SESSION[SessionManager::VAR_LOGGED_IN]);
        $this->assertEquals(Utils::getClientIp(),  $_SESSION[SessionManager::VAR_CLIENT_IP]);
        $this->assertEquals(Utils::getUserAgent(), $_SESSION[SessionManager::VAR_USER_AGENT]);*/
    }
    
    /**
     * @depends testValidateWithDifferentUserAgent
     */
    public function testDestroyUnsetsVariablesAndSession()
    {
        $variableName = 'variable';
        
        $_SESSION[$variableName] = 'value';
        
        SessionManager::destroy();
        
        $this->assertArrayNotHasKey($variableName, $_SESSION);
        $this->assertEmpty(session_id());
    }
}
