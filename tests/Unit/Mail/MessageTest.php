<?php

namespace Tests\Unit\Mail;

use App\Contracts\Mail\Courier;
use App\Mail\Mail;
use Tests\TestCase;

class MessageTest extends TestCase
{
    /**
     * Mail specific config
     */
    protected $config;

    public function setUp(): void
    {
        parent::setUp();
        $this->config = array_merge(
            $this->app->getContainer()->get('settings')['mail'],
            [
                'host' => 'smtp.mailtrap.io',
                'username' => '6d3832de32f5bf',
                'password' => '92191dfc132673',
            ]
        );
    }

    /** @test */
    public function can_set_a_recipient()
    {
        $mail = (new Mail())->to($sendTo = 'hello@example.com');
        $this->assertEquals($sendTo, $mail->getRecipient());
    }

    /** @test */
    public function can_set_a_subject()
    {
        $mail = (new Mail())->subject($subject = 'Example');
        $this->assertEquals($subject, $mail->getSubject());
    }

    /** @test */
    public function can_set_a_body()
    {
        $mail = (new Mail())->body($body = 'This is an example');
        $this->assertEquals($body, $mail->getBody());
    }

    /** @test */
    public function can_set_a_html_body()
    {
        $mail = (new Mail())->html($html = 'This is an example');
        $this->assertEquals($html, $mail->getHtml());
    }

    /** @test */
    public function can_send_mail()
    {
        /**
         * Get mock for mail courier interface
         * 
         * @var \App\Contracts\Mail\Courier $courier
         */
        $courier = $this->createMock(Courier::class);

        // Force the 'send' method of the mock to return 1 which indicates success.
        $courier->method('send')->willReturn(1);

        $result = (new Mail())
            ->to('jmcmah15@gmail.com')
            ->subject('Unit Test: MailTest')
            ->body('This is a unit test in MailTest.php')
            ->html("<b>Hi!</b><br/>This is a unit test message.<br/><br/><b>J</b>abit")
            ->send($courier);

        $this->assertEquals(1, $result);
    }
}
