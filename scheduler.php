<?php

use App\Jobs\SendDueReminders;
use App\Services\Scheduler\Kernel;

// This will give us access to $container and $app
require_once __DIR__ . '/bootstrap/app.php';

$kernel = new Kernel;

// This adds Tasks to the Kernel
(new SendDueReminders($kernel, $container))->run();

$kernel->run();
