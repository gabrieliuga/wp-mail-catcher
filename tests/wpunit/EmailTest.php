<?php

use WpMailCatcher\Models\Logs;

class EmailTest extends \Codeception\TestCase\WPTestCase
{
    private $assetsDir;

    public function __construct($name = null, array $data = [], $dataName = '')
    {
        $this->assetsDir = __DIR__ . '/../_data/';
        parent::__construct($name, $data, $dataName);
    }

    public function setUp()
    {
        parent::setUp();
        Logs::truncate();
    }

    public function testMail()
    {
        $to = 'test@test.com';
        $subject = 'subject';
        $message = 'message';
        $additionalHeaders = ['Content-type: text/html', 'cc: test1@test.com'];

        $imgAttachmentId = $this->factory()->attachment->create_upload_object($this->assetsDir . 'img-attachment.png');
        $pdfAttachmentId = $this->factory()->attachment->create_upload_object($this->assetsDir . 'pdf-attachment.pdf');

        wp_mail($to, $subject, $message, $additionalHeaders, [
            get_attached_file($imgAttachmentId),
            get_attached_file($pdfAttachmentId)
        ]);

        $emailLog = Logs::get()[0];

        $this->assertEquals($to, $emailLog['email_to']);
        $this->assertEquals($subject, $emailLog['subject']);
        $this->assertEquals($message, $emailLog['message']);

        $this->assertEquals($additionalHeaders[0], $emailLog['additional_headers'][0]);
        $this->assertEquals($additionalHeaders[1], $emailLog['additional_headers'][1]);

        $this->assertEquals($imgAttachmentId, $emailLog['attachments'][0]['id']);
        $this->assertEquals(wp_get_attachment_url($imgAttachmentId), $emailLog['attachments'][0]['url']);

        $this->assertEquals($pdfAttachmentId, $emailLog['attachments'][1]['id']);
        $this->assertEquals(wp_get_attachment_url($pdfAttachmentId), $emailLog['attachments'][1]['url']);

        wp_delete_attachment($imgAttachmentId);
        wp_delete_attachment($pdfAttachmentId);
    }

    public function testCorrectTos()
    {
        wp_mail('test@test.com', 'subject', 'message');
        $this->assertTrue(Logs::get()[0]['status']);
    }

    public function testIncorrectTos()
    {
        var_dump(wp_mail('testtest.com', 'subject', 'message'));
        $this->assertFalse(Logs::get()[0]['status']);
    }

    public function testHtmlEmail()
    {
        wp_mail('test@test.com', 'subject', 'message', ['Content-Type: text/html']);
        $this->assertTrue(Logs::get()[0]['is_html']);
    }

    public function testNonHtmlEmail()
    {
        wp_mail('test@test.com', 'subject', 'message');
        $this->assertFalse(Logs::get()[0]['is_html']);
    }
}
