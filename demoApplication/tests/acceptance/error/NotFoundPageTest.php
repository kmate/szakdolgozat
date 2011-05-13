<?php

namespace app\tests\acceptance\error;

use app\tests\acceptance\LoggedInAcceptanceTestCase;

require_once __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'LoggedInAcceptanceTestCase.php';

class NotFoundPageTest extends LoggedInAcceptanceTestCase
{
    public function testShowsNotFoundPageOnInvalidRoute()
    {
        $this->open('not/found');
        $this->waitForPageToLoad('30000');
        $this->assertTextPresent('A keresett lap nem található!');
        $this->assertElementPresent('css=a[href$="task/list"]:contains("Ugrás a feladatlistához.")');
    }
}