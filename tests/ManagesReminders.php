<?php

namespace Tests;

use App\Domain\Reminders\Models\Reminder;
use App\Domain\Reminders\ReminderData;
use App\Models\User;

trait ManagesReminders
{
    /**
     * @param \Carbon\Carbon $date
     * @return ReminderData
     */
    protected function makeReminderData($date = null)
    {
        if (!$date)
            $date = \Carbon\Carbon::now(new \DateTimeZone('Europe/London'));

        return new ReminderData([
            'body' => 'Test Reminder',
            'date' => $date->day,
            // Carbon is not zero based for months, JS is so this accurately reflects
            // how ReminderData will be instantiated from the API request.
            'month' => $date->month - 1,
            'year' => $date->year,
            'time' => "{$date->hour}:{$date->minute}",
            'frequency' => "none",
        ]);
    }

    /**
     * @param int $count
     * @param \Carbon\Carbon $date
     * @return Reminder|Reminder[]
     */
    protected function makeReminder($count = 1, $date = null)
    {
        $reminders = [];
        // Create the required amount of Reminders
        for ($i = 0; $i < $count; $i++) {
            $reminders[] = new Reminder($this->makeReminderData($date)->toArray());
        }
        // If amount required is 1, return just the single instance.
        return $count == 1 ? $reminders[0] : $reminders;
    }

    /**
     * @param int $count
     * @param \Carbon\Carbon $date
     * @return Reminder|Reminder[]
     */
    protected function createReminder($count = 1, $date = null)
    {
        // Note: if count is 1 then makeReminder returns the instance, not an array.
        /** @var Reminder[] */
        $reminders = $this->makeReminder($count, $date);

        // If just one Reminder created, save it then return it.
        if ($count == 1) return tap($reminders)->save();

        // Else loop over each Reminder, save it then return the array.
        foreach ($reminders as $reminder) {
            $reminder->save();
        }
        return $reminders;
    }

    /**
     * @param User $user
     * @param int $count
     * @param \Carbon\Carbon $date
     * @return Reminder|Reminder[]
     */
    protected function createReminderFor(User $user, $count = 1, $date = null)
    {
        $reminders = $this->createReminder($count, $date);

        // If not an array, make it an array so that we can use the saveMany method.
        if (!is_array($reminders)) $reminders = [$reminders];

        $user->reminders()->saveMany($reminders);

        return count($reminders) == 1 ? $reminders[0] : $reminders;
    }
}
