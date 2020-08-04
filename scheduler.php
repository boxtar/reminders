<?php

use App\SchedulerJobs\AddDueReminders;
use App\Services\Scheduler\Kernel;

// This will give us access to $container and $app
require_once __DIR__ . '/bootstrap/app.php';

$kernel = new Kernel;

// This adds Tasks to the Kernel
(new AddDueReminders($kernel, $container))->run();

// Run all tasks added to kernel in previous job(s)
$kernel->run();
