<?php

namespace app\tests\acceptance;

require_once 'AcceptanceTestCase.php';

class LoggedInAcceptanceTestCase extends AcceptanceTestCase
{
    public function start()
    {
        parent::start();
        
        $this->open('user/login');
        $this->type('userName', 'test1');
        $this->type('password', 'password1');
        $this->clickAndWait('login');
        $this->assertElementPresent('css=a[href$="/user/logout"]:contains("Kijelentkezés")');
        $this->assertText('css=#userInfo em', 'Teszt Felhasználó');
    }
}