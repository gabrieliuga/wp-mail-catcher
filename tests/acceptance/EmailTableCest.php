<?php

use WpMailCatcher\GeneralHelper;

class EmailTableCest
{
    public function _before(AcceptanceTester $I)
    {
        $I->maximizeWindow();
        $I->loginAsAdmin();
        $I->amOnAdminPage('admin.php?page=' . GeneralHelper::$adminPageSlug);
    }

    public function _openNewMessageModal(AcceptanceTester $I)
    {
        $I->click('[data-codeception="new-message-modal-btn"]');
        $I->seeElement('[data-codeception="new-message-modal-body"]');
    }

    public function _sendNewMessage(AcceptanceTester $I, $messageArgs)
    {
        $this->_openNewMessageModal($I);
        $I->selectOption('[data-codeception="header-select"]', 'to');
        $I->fillField('[data-codeception="header-input"]', $messageArgs['to']);
        $I->fillField('subject', $messageArgs['subject']);
        $I->click('button[type=submit]');
    }

    public function _doBulkAction(AcceptanceTester $I, $checkboxSelector, $optionName, $optionValue, $submitBtnSelector)
    {
        $I->checkOption($checkboxSelector);
        $I->selectOption($optionName, $optionValue);
        $I->click($submitBtnSelector);
    }

    public function _doBulkActionUsingTopOfTableForm(AcceptanceTester $I, $actionName)
    {
        $this->_doBulkAction(
            $I,
            '#cb-select-all-1',
            'action',
            $actionName,
            '#doaction'
        );
    }

    public function _doBulkActionUsingBottomOfTableForm(AcceptanceTester $I, $actionName)
    {
        $this->_doBulkAction(
            $I,
            '#cb-select-all-2',
            'action2',
            $actionName,
            '#doaction2'
        );
    }

    public function testBulkDeleteLogsUsingTopOfTableForm(AcceptanceTester $I)
    {
        $to = 'test@test.com';
        $subject = 'my subject';

        $this->_sendNewMessage($I, [
            'to' => $to,
            'subject' => $subject
        ]);

        $I->see($to);
        $I->see($subject);
        $this->_doBulkActionUsingTopOfTableForm($I, 'delete');
        $I->dontSee($to);
        $I->dontSee($subject);
    }

    public function testBulkDeleteLogsUsingBottomOfTableForm(AcceptanceTester $I)
    {
        $to = 'test1@test.com';
        $subject = 'my subject1';

        $this->_sendNewMessage($I, [
            'to' => $to,
            'subject' => $subject
        ]);

        $I->see($to);
        $I->see($subject);
        $this->_doBulkActionUsingBottomOfTableForm($I, 'delete');
        $I->dontSee($to);
        $I->dontSee($subject);
    }
}

