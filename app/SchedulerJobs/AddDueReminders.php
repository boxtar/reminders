<?php

namespace App\SchedulerJobs;

use DI\Container;
use App\Tasks\SendReminder;
use App\Services\Scheduler\Kernel;
use App\Domain\Reminders\Models\Reminder;
use Carbon\Carbon;

class AddDueReminders
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
        // @todo: I need to figure out the whole timezone thing...
        Reminder::canBeSent()
            ->with('owner')
            ->get()
            ->each(function (Reminder $reminder) {
                // Add SendReminder task to Scheduler for execution
                // @todo: re-write without the kernel/scheduler?
                $this->kernel
                    ->add(new SendReminder($reminder, $this->container->get('notifications.broadcaster')))
                    ->cron("* * * * *");
            });
    }
}
