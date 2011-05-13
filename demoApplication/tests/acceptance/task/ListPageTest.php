<?php

namespace app\tests\acceptance\task;

use app\tests\acceptance\LoggedInAcceptanceTestCase;

require_once __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'LoggedInAcceptanceTestCase.php';

class ListPageTest extends LoggedInAcceptanceTestCase
{
    public function testTableHeadersAreCorrectAndNewLinkPresent()
    {
        $this->assertElementPresent('css=a[href$="task/new"]:contains("Új feladat")');
        
        $this->assertElementPresent('css=#ownList thead th:contains("Műveletek")');
        $this->assertElementPresent('css=#ownList thead th:contains("Publikus")');
        $this->assertElementNotPresent('css=#ownList thead th:contains("Felhasználó")');
        
        $this->assertElementNotPresent('css=#publicList thead th:contains("Műveletek")');
        $this->assertElementNotPresent('css=#publicList thead th:contains("Publikus")');
        $this->assertElementPresent('css=#publicList thead th:contains("Felhasználó")');
    }
}