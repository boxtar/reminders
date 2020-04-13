<?php

declare(strict_types=1);

namespace Tests;

use App\Domain\Reminders\Models\Reminder;
use App\Domain\Reminders\ReminderData;
use Slim\App;
use DI\Container;
use Dotenv\Dotenv;
use App\Models\User;
use Slim\Psr7\Request;
use Slim\Factory\AppFactory;
use Slim\Psr7\Factory\RequestFactory;
use Psr\Http\Message\RequestInterface;
use PHPUnit\Framework\TestCase as PHPUnitTestCase;
use Psr\Http\Message\ResponseInterface;

abstract class TestCase extends PHPUnitTestCase
{

    /**
     * @var App
     */
    protected $app;

    /**
     * @var bool
     */
    protected $setUpHasRun = false;

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var ResponseInterface
     */
    protected $response;

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

    protected function makeReminderData()
    {
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

    protected function makeReminder()
    {
        return new Reminder($this->makeReminderData()->toArray());
    }

    /**
     * @return void
     */
    protected function tearDown(): void
    {
        if ($this->app) {
            $this->app = null;
        }
        $this->setUpHasRun = false;
    }

    protected function signIn()
    {
        // Need a session to set authenticated user
        session_start();
        $user = new User([
            'name' => 'Test User',
            'email' => 'test@ayex.co.uk',
            'password' => password_hash('admin1', PASSWORD_BCRYPT)
        ]);

        $this->app->getContainer()->get('auth')->attempt($user, 'admin1');

        return $user;
    }

    protected function createRequest($method, $uri)
    {
        $this->request = (new RequestFactory())->createRequest($method, $uri);
        return $this;
    }

    protected function get($uri)
    {
        $this->createRequest('GET', $uri);
        $this->response = $this->app->handle($this->request);
        return $this;
    }

    protected function post($uri, $data = [])
    {
        $this->createRequest('POST', $uri);
        $this->request = $this->request->withParsedBody($data);
        $this->response = $this->app->handle($this->request);
        return $this;
    }

    protected function getResponseStatusCode()
    {
        return $this->response->getStatusCode();
    }

    protected function getResponseBody()
    {
        return (string) $this->response->getBody();
    }

    protected function parseJsonResponse()
    {
        return json_decode($this->getResponseBody());
    }
}
