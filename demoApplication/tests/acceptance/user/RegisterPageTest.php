<?php

namespace app\tests\acceptance\user;

use app\tests\acceptance\AcceptanceTestCase;

require_once __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'AcceptanceTestCase.php';

class RegisterPageTest extends AcceptanceTestCase
{
    public function testFormElementsAndRegisterLinkPresent()
    {
        $this->open('user/register');
        $this->assertTextPresent('Regisztráció');
        $this->assertElementPresent('css=a[href$="user/login"]:contains("Bejelentkezés")');
        $this->assertElementPresent('css=input[type="text"][name="userName"]');
        $this->assertElementPresent('css=input[type="text"][name="fullName"]');
        $this->assertElementPresent('css=input[type="text"][name="email"]');
        $this->assertElementPresent('css=input[type="password"][name="password"]');
        $this->assertElementPresent('css=input[type="password"][name="password2"]');
        $this->assertElementPresent('css=input[type="submit"][name="register"]');
    }
    
    public function testValidationErrorsPresentOnEmptyFormSubmit()
    {
        $this->open('user/register');
        $this->clickAndWait('register');
        $this->assertElementPresent('css=form table tr.validationError');
    }
    
    public function testErrorPresentOnUserNameUniqueCheckFail()
    {
        $this->open('user/register');
        $this->type('userName',  'test1');
        $this->type('fullName',  'Duplázott Teszt Felhasználó');
        $this->type('email',     'test@test.hu');
        $this->type('password',  'jelszo');
        $this->type('password2', 'jelszo');
        $this->clickAndWait('register');
        $this->assertTextPresent('A megadott felhasználónév már foglalt!');
    }
    
    public function testErrorPresentOnEmailUniqueCheckFail()
    {
        $this->open('user/register');
        $this->type('userName',  'test10');
        $this->type('fullName',  'Teszt Felhasználó');
        $this->type('email',     'test@localhost.hu');
        $this->type('password',  'jelszo');
        $this->type('password2', 'jelszo');
        $this->clickAndWait('register');
        $this->assertTextPresent('A megadott e-mail cím már foglalt!');
    }
    
    public function testLoginLinkIsPresentOnSuccessfulRegistration()
    {
        $this->open('user/register');
        $this->type('userName',  'test10');
        $this->type('fullName',  'Teszt Felhasználó');
        $this->type('email',     'test10@localhost.hu');
        $this->type('password',  'jelszo');
        $this->type('password2', 'jelszo');
        $this->clickAndWait('register');
        $this->assertTextPresent('Regisztrációja sikeres volt. Kérem jelentkezzen be.');
        $this->assertElementPresent('css=a[href$="user/login"]');
    }
}