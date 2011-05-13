<?php

namespace app\tests\acceptance\task;

use app\tests\acceptance\LoggedInAcceptanceTestCase;

require_once __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'LoggedInAcceptanceTestCase.php';

class TaskIdParameterTestCase extends LoggedInAcceptanceTestCase
{
    protected function _runShowErrorPageIfTaskDoesNotExistsTest($action)
    {
        $this->open('task/' . $action . '/userId/110');
        $this->waitForPageToLoad('30000');
        $this->assertTextPresent('Hiba');
        $this->assertTextPresent('A megadott feladat nem létezik!');
        $this->assertElementPresent('css=a[href$="task/list"]:contains("Vissza a feladatlistához")');
    }
    
    protected function _runShowErrorPageIfTaskIdDoesNotProvidedTest($action)
    {
        $this->open('task/' . $action);
        $this->waitForPageToLoad('30000');
        $this->assertTextPresent('Hiba');
        $this->assertTextPresent('A megadott feladat nem létezik!');
        $this->assertElementPresent('css=a[href$="task/list"]:contains("Vissza a feladatlistához")');
    }
    
    protected function _runShowErrorPageIfTaskIsNotPublicOrOwnTest($action)
    {
        $this->open('task/' . $action . '/taskId/2');
        $this->waitForPageToLoad('30000');
        $this->assertTextPresent('Hiba');
        $this->assertElementPresent('css=a[href$="task/list"]:contains("Vissza a feladatlistához")');
    }
}