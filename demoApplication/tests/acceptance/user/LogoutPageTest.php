<?php

namespace app\tests\acceptance\user;

use app\tests\acceptance\LoggedInAcceptanceTestCase;

require_once __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'LoggedInAcceptanceTestCase.php';

class LogoutPageTest extends LoggedInAcceptanceTestCase
{
    public function testLogoutDeletesCookieAndForwardsToLogin()
    {
        $loggedInSessionId = $this->getCookieByName(TEST_SESSION_NAME);
        
        $this->clickAndWait('css=a[href$="/user/logout"]:contains("Kijelentkezés")');
        $this->assertLocation('/task/index$');
        $this->assertTextPresent('Bejelentkezés');
        
        $sessionIdAfterLogout = $this->getCookieByName(TEST_SESSION_NAME);
        
        $this->assertNotEquals($loggedInSessionId, $sessionIdAfterLogout);
    }
}