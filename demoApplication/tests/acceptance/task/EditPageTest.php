<?php

namespace app\tests\acceptance\task;

require_once 'TaskIdParameterTestCase.php';

class EditPageTest extends TaskIdParameterTestCase
{
    public function testFormElementsFilledCorrectlyAndBackLinkPresent()
    {
        $this->clickAndWait('css=a[href$=task/edit/taskId/1]:contains("szerkesztés")');
        $this->assertLocation('/task/edit');
        $this->assertTextPresent('Feladat szerkesztése');
        $this->assertElementPresent('css=a[href$="task/list"]');
        $this->assertValue('title',       'Feladat 1');
        $this->assertValue('description', 'Leírás 1');
        $this->assertValue('start',       '2011. 02. 11.');
        $this->assertValue('finish',      '2011. 03. 12.');
        $this->assertElementPresent('css=input[type="radio"][name="priority"][value="low"][checked]');
        $this->assertElementPresent('css=input[type="radio"][name="priority"][value="normal"]');
        $this->assertElementPresent('css=input[type="radio"][name="priority"][value="high"]');
        $this->assertValue('css=input[type="checkbox"][name="is_public"]', 'on');
        $this->assertElementPresent('css=input[type="submit"][name="update"]');
    }
    
    public function testValidationErrorsPresentOnEmptyFormSubmit()
    {
        $this->open('task/edit/taskId/1');
        $this->type('title', '');
        $this->clickAndWait('update');
        $this->assertElementPresent('css=form table tr.validationError');
    }
    
    public function testLinksArePresentAndViewWorksOnSuccessfulOperation()
    {
        $this->clickAndWait('css=a[href$=task/edit/taskId/1]:contains("szerkesztés")');
        $this->type('title',       'Feladat 1 - módosítva');
        $this->type('description', 'Leírás 1 - módosítva');
        $this->type('start',       '2011. 05. 02.');
        $this->type('finish',      '2011. 05. 03.');
        $this->click('css=input[type="radio"][name="priority"][value="normal"]');
        $this->click('css=input[type="checkbox"][name="is_public"]');
        $this->clickAndWait('update');
        $this->assertTextPresent('A feladat adatainak módosítása sikeres.');
        $this->assertElementPresent('css=a[href$="task/list"]');
        
        $this->clickAndWait('link=Megtekintés');
        $this->assertLocation('/task/view/taskId/1');
        $this->assertTable('css=table.0.1', 'Feladat 1 - módosítva');
        $this->assertTable('css=table.1.1', 'Leírás 1 - módosítva');
        $this->assertTable('css=table.2.1', '2011. 05. 02.');
        $this->assertTable('css=table.3.1', '2011. 05. 03.');
        $this->assertTable('css=table.4.1', 'normál');
        $this->assertTable('css=table.5.1', 'nem');
    }
    
    public function testShowErrorPageIfTaskDoesNotExists()
    {
        $this->_runShowErrorPageIfTaskDoesNotExistsTest('edit');
    }
    
    public function testShowErrorPageIfTaskIdDoesNotProvided()
    {
        $this->_runShowErrorPageIfTaskIdDoesNotProvidedTest('edit');
    }
    
    public function testShowErrorPageIfTaskIsNotOwn()
    {
        $this->_runShowErrorPageIfTaskIsNotPublicOrOwnTest('edit');
    }
}