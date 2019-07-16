<?php

$I = new AcceptanceTester( $scenario );
$I->loginAsAdmin();
//$I->amOnPluginsPage();
$I->activatePlugin('wp-mail-catcher');
$I->amOnPage('admin.php?page=wp-mail-catcher');
$I->click('[data-target="#new-message"]');
$I->see(' Is HTML email?');

