<?php

namespace Tests;

use PHPUnit\Framework\TestCase as PHPUnitTestCase;

abstract class TestCase extends PHPUnitTestCase
{
    protected $app;

    protected $setUpHasRun = false;

    /**
     * Setup the test environment.
     *
     * @return void
     */
    protected function setUp(): void
    {
        if (!$this->app) {
            $this->app = require __DIR__ . '/../bootstrap/app.php';
        }

        $this->setUpHasRun = true;
    }

    protected function tearDown(): void
    {
        if ($this->app) {
            $this->app = null;
        }
        // session_destroy();
        $this->setUpHasRun = false;
    }
}
