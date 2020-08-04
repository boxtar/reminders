<?php

// This will give us access to $container and $app

use App\Jobs\SetNextRunDate;

require_once __DIR__ . '/bootstrap/app.php';

// This adds Tasks to the Kernel
(new SetNextRunDate())->run();
