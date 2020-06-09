<?php

namespace App\Jobs;

use DI\Container;
use App\Tasks\SendReminder;
use App\Services\Scheduler\Kernel;
use App\Domain\Reminders\Models\Reminder;

class SendDueReminders
{
    /**
     * This is provided through the constructor.
     * @var Kernel
     */
    protected $kernel;

    /**
     * @var Container
     */
    protected $container;

    public function __construct(Kernel $kernel, Container $container)
    {
        $this->kernel = $kernel;
        $this->container = $container;
    }

    public function run()
    {
        // Loop through every Reminder... 
        // Wouldn't it be better to scope them to reminders not in the future?
        // To be safe, perhaps an hour into future?
        Reminder::all()->each(function (Reminder $reminder) {
            // If the initial reminder hasn't yet run
            // or, if it has and the reminder is recurring
            // then we need to send the reminder out.
            if (!$reminder->hasInitialReminderRun() || $reminder->isRecurring()) {
                $this->kernel
                    ->add(new SendReminder($reminder, $this->container->get('notifications.broadcaster')))
                    ->cron($reminder->getCronExpression());
            }
        });
    }
}
