<?php

namespace app\tests\acceptance\user;

use app\tests\acceptance\AcceptanceTestCase;

require_once __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'AcceptanceTestCase.php';

class LoginPageTest extends AcceptanceTestCase
{
    public function testFormElementsAndRegisterLinkPresent()
    {
        $this->open('user/login');
        $this->assertTextPresent('Bejelentkezés');
        $this->assertElementPresent('css=a[href$="user/register"]:contains("Regisztráció")');
        $this->assertElementPresent('css=input[type="text"][name="userName"]');
        $this->assertElementPresent('css=input[type="password"][name="password"]');
        $this->assertElementPresent('css=input[type="submit"][name="login"]');
    }
    
    public function testValidationErrorsPresentOnEmptyFormSubmit()
    {
        $this->open('user/login');
        $this->clickAndWait('login');
        $this->assertTable('css=form table.1.0', '*Ezt a mezőt kötelező kitölteni!');
        $this->assertTable('css=form table.3.0', '*Ezt a mezőt kötelező kitölteni!');
    }
    
    public function testErrorPresentOnInvalidCredentials()
    {
        $this->open('user/login');
        $this->type('userName', 'invalidUserName');
        $this->type('password', 'invalidPassword');
        $this->clickAndWait('login');
        $this->assertTextPresent('Helyetelen felhasználónév vagy jelszó!');
    }
    
    public function testForwardsToTaskListOnValidCredentials()
    {
        $this->open('user/login');
        $this->type('userName', 'test2');
        $this->type('password', 'password2');
        $this->clickAndWait('login');
        $this->assertLocation('/task/list$');
    }
}