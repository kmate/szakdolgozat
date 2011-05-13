<?php

namespace app\tests\acceptance\user;

use app\tests\acceptance\LoggedInAcceptanceTestCase;

require_once __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'LoggedInAcceptanceTestCase.php';

class ViewPageTest extends LoggedInAcceptanceTestCase
{
    public function testDataElementsAndBackLinkPresent()
    {
        $this->clickAndWait('link=Második Teszt Felhasználó');
        $this->assertLocation('/user/view/userId/2');
        $this->assertTextPresent('Felhasználói adatlap');
        $this->assertTable('css=table.0.1', 'Második Teszt Felhasználó');
        $this->assertTable('css=table.1.1', 'test2@localhost.hu');
        $this->assertElementPresent('css=a[href$="task/list"]:contains("Vissza")');
    }
    
    public function testForwardsToTaskListIfUserDoesNotExists()
    {
        $this->open('user/view/userId/200');
        $this->waitForPageToLoad('30000');
        $this->assertLocation('/task/list');
    }
    
    public function testForwardsToTaskListIfUserIdDoesNotProvided()
    {
        $this->open('user/view');
        $this->waitForPageToLoad('30000');
        $this->assertLocation('/task/list');
    }
}