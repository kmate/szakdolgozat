<?php

namespace app\tests\acceptance\task;

require_once 'TaskIdParameterTestCase.php';

class ViewPageTest extends TaskIdParameterTestCase
{
    public function testDataElementsAndBackLinkPresent()
    {
        $this->clickAndWait('link=Feladat 11');
        $this->assertLocation('/task/view/taskId/11');
        $this->assertTextPresent('Feladat megtekintése');
        $this->assertTextPresent('Feladat 11');
        $this->assertTextPresent('2011. 03. 12.');
        $this->assertTextPresent('2011. 04. 21.');
        $this->assertTextPresent('normál');
        $this->assertTextPresent('igen');
        $this->assertElementPresent('css=a[href$="task/list"]:contains("Vissza a listához")');
    }
    
    public function testUserFullNameIsPresentAtPublicTaskView()
    {
        $this->clickAndWait('link=Feladat 5');
        $this->assertLocation('/task/view/taskId/5');
        $this->assertTextPresent('Felhasználó:');
        $this->assertTextPresent('Harmadik Teszt Felhasználó');
    }
    
    public function testShowErrorPageIfTaskDoesNotExists()
    {
        $this->_runShowErrorPageIfTaskDoesNotExistsTest('view');
    }
    
    public function testShowErrorPageIfTaskIdDoesNotProvided()
    {
        $this->_runShowErrorPageIfTaskIdDoesNotProvidedTest('view');
    }
    
    public function testShowErrorPageIfTaskIsNotPublicOrOwn()
    {
        $this->_runShowErrorPageIfTaskIsNotPublicOrOwnTest('view');
    }
}