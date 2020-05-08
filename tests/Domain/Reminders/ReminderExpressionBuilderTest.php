<?php

namespace Tests\Domain\Reminders;

use App\Domain\Reminders\ReminderData;
use App\Domain\Reminders\ReminderExpressionBuilder;
use Tests\TestCase;

class ReminderExpressionBuilderTest extends TestCase
{
    /** @test */
    public function it_returns_the_correct_cron_expression()
    {
        $data = $this->makeReminderData();
        $builder = ReminderExpressionBuilder::createFromReminderData($data);
        $month = $data->month + 1; // PHP month is 0 based, Cron month is 1 based.
        $this->assertEquals(
            "{$data->minute} {$data->hour} {$data->date} {$month} *",
            $builder->build()
        );
    }
}
