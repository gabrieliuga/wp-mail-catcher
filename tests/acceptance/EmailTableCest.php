<?php

class EmailTableCest
{
    private $validTo = 'test@test.com';
    private $validSubject = 'my subject';

    public function testCanOpenNewMessageModal(AcceptanceTester $I)
    {
        $I->maximizeWindow();
        $I->loginAsAdmin();
        $I->amOnAdminPage('admin.php?page=wp-mail-catcher');

        $this->openNewMessageModal($I);
        $this->sendValidMessage($I);

        $I->see($this->validTo);
        $I->see($this->validSubject);
        $I->checkOption('#cb-select-all-1');
        $I->selectOption('action', 'delete');
        $I->click('#doaction');
        $I->dontSee($this->validTo);
        $I->dontSee($this->validSubject);

        $I->makeScreenshot();
    }

    private function openNewMessageModal(AcceptanceTester $I)
    {
        $I->click('[data-codeception="new-message-modal-btn"]');
        $I->seeElement('[data-codeception="new-message-modal-body"]');
    }

    private function sendValidMessage(AcceptanceTester $I)
    {
        $I->selectOption('[data-codeception="header-select"]', 'to');
        $I->fillField('[data-codeception="header-input"]', $this->validTo);
        $I->fillField('subject', $this->validSubject);
        $I->click('button[type=submit]');
    }
}

