<?php

class EmailTableCest
{
    private $validTo = 'test@test.com';
    private $validSubject = 'my subject';

    public function _before(AcceptanceTester $I)
    {
        $I->maximizeWindow();
        $I->loginAsAdmin();
        $I->amOnAdminPage('admin.php?page=wp-mail-catcher');
    }

    public function testCanOpenNewMessageModal(AcceptanceTester $I)
    {
        $I->click('[data-codeception="new-message-modal-btn"]');
        $I->seeElement('[data-codeception="new-message-modal-body"]');
    }

    public function testCanSendValidMessage(AcceptanceTester $I)
    {
        $this->testCanOpenNewMessageModal($I);
        $I->selectOption('[data-codeception="header-select"]', 'to');
        $I->fillField('[data-codeception="header-input"]', $this->validTo);
        $I->fillField('subject', $this->validSubject);
        $I->click('button[type=submit]');
    }

    public function testBulkDeleteLogs(AcceptanceTester $I)
    {
        $this->testCanSendValidMessage($I);
        $I->see($this->validTo);
        $I->see($this->validSubject);
        $I->checkOption('#cb-select-all-1');
        $I->selectOption('action', 'delete');
        $I->click('#doaction');
        $I->dontSee($this->validTo);
        $I->dontSee($this->validSubject);

        $I->makeScreenshot();
    }
}

