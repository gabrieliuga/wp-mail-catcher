<?php

class EmailTableCest
{
//    public function __construct(AcceptanceTester $I)
//    {
//    }
//
//    public function _before(AcceptanceTester $I)
//    {
////        $I->amOnPluginsPage();
////        $I->seePluginActivated('wp-mail-catcher');
//        $I->loginAsAdmin();
//        $I->amOnAdminPage('admin.php?page=wp-mail-catcher');
//    }

    public function testCanOpenNewMessageModal(AcceptanceTester $I)
    {
        $I->loginAsAdmin();
        $I->amOnAdminPage('admin.php?page=wp-mail-catcher');

        $I->click('[data-target="#new-message"]');
        $I->see(' Is HTML email?');

//        $I->fillField('.field-block:first-of-type .-input', 'test@test.com');
        $I->fillField('subject', 'hello world');
        $I->scrollTo('button[type=submit]');
        $I->click('button[type=submit]');
        $I->makeScreenshot();
    }

//    public function testCanSendMessageUsingModal(AcceptanceTester $I)
//    {
//        $I->click('[data-target="#new-message"]');
//        $I->see(' Is HTML email?');
//        $I->fillField('.field.-input:first-of-type', 'test@test.com');
//        $I->makeScreenshot();
//    }
}

