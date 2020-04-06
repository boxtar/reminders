<?php

namespace Tests\Unit\Mail;

use App\Services\Mail\Contracts\Courier;
use App\Services\Mail\Message;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

class MessageTest extends TestCase
{
    /**
     * Mail specific config
     * @var array
     */
    protected $config;

    /** @test */
    public function can_set_a_recipient()
    {
        /**
         * @var Courier
         */
        $courier = $this->createMock(Courier::class);

        $mail = (new Message($courier))->to($sendTo = 'hello@example.com');
        $this->assertEquals($sendTo, $mail->getRecipient());
    }

    /** @test */
    public function can_set_a_subject()
    {
        /**
         * @var Courier
         */
        $courier = $this->createMock(Courier::class);

        $mail = (new Message($courier))->subject($subject = 'Example');
        $this->assertEquals($subject, $mail->getSubject());
    }

    /** @test */
    public function can_set_a_body()
    {
        /**
         * @var Courier
         */
        $courier = $this->createMock(Courier::class);

        $mail = (new Message($courier))->body($body = 'This is an example');
        $this->assertEquals($body, $mail->getBody());
    }

    /** @test */
    public function can_set_a_html_body()
    {
        /**
         * @var Courier
         */
        $courier = $this->createMock(Courier::class);

        $mail = (new Message($courier))->html($html = 'This is an example');
        $this->assertEquals($html, $mail->getHtml());
    }

    /** @test */
    public function can_send_mail()
    {
        /**
         * @var Courier|MockObject
         */
        $courier = $this->getMockBuilder(Courier::class)->setMethods(['send'])->getMock();
        $courier->expects($this->once())->method('send')->willReturn(1);

        $result = (new Message($courier))
            ->to('unittest@reminders.ayex.co.uk')
            ->subject('Unit Test: MailTest')
            ->body('This is a unit test in MailTest.php')
            ->html("<b>Hi!</b><br/>This is a unit test message.<br/><br/><b>J</b>abit")
            ->send($courier);

        $this->assertEquals(1, $result);
    }
}
