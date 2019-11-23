<?php

use App\Events\SendReminder;
use App\Models\Reminder;
use App\Scheduler\Kernel;

// This will give us access to $container and $app
require_once __DIR__ . '/bootstrap/app.php';

$kernel = new Kernel;

// Implement this is a way where we can register classes to
// handle the adding of Tasks to the Kernel to avoid
// cluttering up this file too much.
Reminder::all()->each(function ($reminder) use ($kernel, $container) {
    $kernel
        ->add(new SendReminder(
            $reminder,
            $container->get('guzzle'),
            $container->get('settings')['telegram']
        ))
        ->cron($reminder->expression);
});

$kernel->run();
