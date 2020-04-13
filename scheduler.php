<?php

use App\Domain\Reminders\Models\Reminder;
use App\Tasks\SendReminder;
use App\Services\Scheduler\Kernel;

// This will give us access to $container and $app
require_once __DIR__ . '/bootstrap/app.php';

$kernel = new Kernel;

// Implement this in a way where we can register classes to
// handle the adding of Tasks to the Kernel to avoid
// cluttering up this file too much.
Reminder::all()->each(function (Reminder $reminder) use ($kernel, $container) {
    if (!$reminder->hasInitialReminderRun() || $reminder->isRecurring()) {
        $kernel
            ->add(new SendReminder($reminder, $container->get('notifications.broadcaster')))
            ->cron($reminder->getCronExpression());
    }
});

$kernel->run();
