<?php

namespace Tests\Application\Jobs;

use Carbon\Carbon;
use Tests\TestCase;
use App\Jobs\SetNextRunDate;
use App\Domain\Reminders\Models\Reminder;
use App\Domain\Reminders\Actions\CreateOrUpdateReminderAction;

class SetNextRunDateTest extends TestCase
{
    /** @test */
    public function it_sets_next_run_date_when_initial_has_not_run()
    {
        CreateOrUpdateReminderAction::create()->execute(
            $this->makeReminderData($date = Carbon::create(2020, 11, 5, 17, 0)),
            $this->signIn()
        );
        (new SetNextRunDate)->run();
        $this->assertEquals($date, Carbon::parse(Reminder::first()->next_run));
    }
}
