<?php

namespace Tests\Application\Tasks;

use App\Services\Notifications\Broadcaster;
use App\Tasks\SendReminder;
use Carbon\Carbon;
use Tests\TestCase;

class SendReminderTest extends TestCase
{
    /** @test */
    public function broadcaster_is_configured_and_invoked_when_task_is_handled_by_scheduler()
    {
        // It doesn't really matter if the due date is in the past, present or future.
        // We using it with the SendReminder task regardless.
        $reminder = $this->createReminderFor($user = $this->signIn());

        // Mock broadcaster
        $broadcaster = $this->getBroadcasterMock();

        (new SendReminder($reminder, $broadcaster))->handle();
    }

    /** @test */
    public function reminder_is_forwarded_after_initial_has_been_sent()
    {
        $initialDueDate = Carbon::now('Europe/London');
        // Create reminder
        $reminder = $this->createReminderFor($user = $this->signIn(), 1, $initialDueDate);
        $reminder->setRecurrenceFrequency('monthly');
        $this->assertTrue($reminder->isRecurring());
        $this->assertEquals($initialDueDate->format('Y-m-d h:m'), $reminder->next_run->format('Y-m-d h:m'));

        // Mock broadcaster
        $broadcaster = $this->getBroadcasterMock();

        (new SendReminder($reminder, $broadcaster))->handle();

        $this->assertTrue($reminder->hasInitialReminderRun());
        $this->assertEquals($initialDueDate->addMonth()->format('Y-m-d h:m'), $reminder->next_run->format('Y-m-d h:m'));
    }

    /**
     * @return Broadcaster
     */
    protected function getBroadcasterMock()
    {
        $broadcaster = $this->getMockBuilder(Broadcaster::class)
            ->disableOriginalConstructor()
            ->setMethods(['message', 'settings', 'send'])
            ->getMock();

        // Setup methods
        $broadcaster->expects($this->once())->method('message')->willReturn($broadcaster);
        $broadcaster->expects($this->once())->method('settings')->willReturn($broadcaster);
        $broadcaster->expects($this->once())->method('send');

        return $broadcaster;
    }
}
