<?php

namespace app\tests\acceptance\task;

require_once 'TaskIdParameterTestCase.php';

class DeletePageTest extends TaskIdParameterTestCase
{
    public function testConfirmationTextAndLinksArePresent()
    {
        $this->clickAndWait('css=a[href$=task/confirm-delete/taskId/10]:contains("törlés")');
        $this->assertTextPresent('Feladat törlése');
        $this->assertTextPresent('Biztosan törli a kiválasztott feladatot?');
        $this->assertElementPresent('css=a[href$="delete/taskId/10"]:contains("Igen")');
        $this->assertElementPresent('css=a[href$="task/list"]:contains("Mégsem")');
    }
    
    public function testBackLinkIsPresentAndTaskRemovedOnSuccessfulDelete()
    {
        $this->clickAndWait('css=a[href$=task/confirm-delete/taskId/10]:contains("törlés")');
        $this->clickAndWait('link=Igen');
        $this->assertTextPresent('Feladat törlése');
        $this->assertTextPresent('A kiválasztott feladat törlése sikeres.');
        $this->clickAndWait('link=Vissza a feladatlistához');
        $this->assertLocation('/task/list');
        $this->assertElementNotPresent('css=a[href$=task/confirm-delete/taskId/10]');
    }
    
    public function testShowErrorPageIfTaskDoesNotExists()
    {
        $this->_runShowErrorPageIfTaskDoesNotExistsTest('delete');
    }
    
    public function testShowErrorPageIfTaskIdDoesNotProvided()
    {
        $this->_runShowErrorPageIfTaskIdDoesNotProvidedTest('delete');
    }
    
    public function testShowErrorPageIfTaskIsNotOwn()
    {
        $this->_runShowErrorPageIfTaskIsNotPublicOrOwnTest('delete');
    }
}