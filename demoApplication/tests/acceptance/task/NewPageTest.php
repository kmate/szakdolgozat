<?php

namespace app\tests\acceptance\task;

use app\tests\acceptance\LoggedInAcceptanceTestCase;

require_once __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'LoggedInAcceptanceTestCase.php';

class NewPageTest extends LoggedInAcceptanceTestCase
{
    public function testFormElementsAndBackLinkPresent()
    {
        $this->clickAndWait('link=Új feladat');
        $this->assertLocation('/task/new');
        $this->assertTextPresent('Új feladat');
        $this->assertElementPresent('css=a[href$="task/list"]');
        $this->assertElementPresent('css=input[type="text"][name="title"]');
        $this->assertElementPresent('css=textarea[name="description"]');
        $this->assertElementPresent('css=input[type="text"][name="start"]');
        $this->assertElementPresent('css=input[type="text"][name="finish"]');
        $this->assertElementPresent('css=input[type="radio"][name="priority"][value="low"]');
        $this->assertElementPresent('css=input[type="radio"][name="priority"][value="normal"]');
        $this->assertElementPresent('css=input[type="radio"][name="priority"][value="high"]');
        $this->assertElementPresent('css=input[type="checkbox"][name="is_public"]');
        $this->assertElementPresent('css=input[type="submit"][name="add"]');
    }
    
    public function testValidationErrorsPresentOnEmptyFormSubmit()
    {
        $this->open('task/new');
        $this->clickAndWait('add');
        $this->assertElementPresent('css=form table tr.validationError');
    }
    
    public function testLinksArePresentAndViewWorksOnSuccessfulOperation()
    {
        $this->open('task/new');
        $this->type('title',       'Feladat 20');
        $this->type('description', 'Leírás 20');
        $this->type('start',       '2011. 05. 02.');
        $this->type('finish',      '2011. 05. 03.');
        $this->type('priority',    'normal');
        $this->type('is_public',   '0');
        $this->clickAndWait('add');
        $this->assertTextPresent('Az új feladat hozzáadása sikeres.');
        $this->assertElementPresent('css=a[href$="task/list"]');
        
        $this->clickAndWait('link=Megtekintés');
        $this->assertLocation('regexp:/task/view/taskId/\d{1,}$');
        $this->assertTable('css=table.0.1', 'Feladat 20');
        $this->assertTable('css=table.1.1', 'Leírás 20');
        $this->assertTable('css=table.2.1', '2011. 05. 02.');
        $this->assertTable('css=table.3.1', '2011. 05. 03.');
        $this->assertTable('css=table.4.1', 'normál');
        $this->assertTable('css=table.5.1', 'nem');
    }
}