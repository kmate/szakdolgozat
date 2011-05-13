<?php

namespace app\tests\acceptance;

use \PHPUnit_Extensions_SeleniumTestCase;

class AcceptanceTests extends PHPUnit_Extensions_SeleniumTestCase
{
    public static $seleneseDirectory = TEST_SCRIPTS_PATH;
    
    public function setUp()
    {
        $this->setBrowserUrl(TEST_BASE_URL);
        
        // TODO: add database filler & login code
        // test login page errors in a separate test case
        
        
    }
}