<?php

declare(strict_types=1);

namespace Tests;

use Slim\App;
use App\Models\User;
use Slim\Psr7\Request;
use Slim\Psr7\Factory\RequestFactory;
use Psr\Http\Message\ResponseInterface;
use App\Domain\Reminders\Models\Reminder;
use PHPUnit\Framework\TestCase as PHPUnitTestCase;

abstract class TestCase extends PHPUnitTestCase
{

    use ManagesReminders;

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
     * @return void
     */
    protected function setUp(): void
    {
        if (!$this->app) {
            $this->app = require __DIR__ . '/../bootstrap/app.php';
        }

        // Truncate models
        User::truncate();
        Reminder::truncate();
        
        $this->setUpHasRun = true;
    }

    /**
     * @return void
     */
    protected function tearDown(): void
    {
        if ($this->app) {
            $this->app = null;
        }

        // Truncate models
        User::truncate();
        Reminder::truncate();

        $this->setUpHasRun = false;
    }

    protected function signIn()
    {
        // Need a session to set authenticated user
        if (session_status() === PHP_SESSION_NONE) session_start();
        $user = new User([
            'name' => 'Johnpaul McMahon',
            'email' => 'jmcmah15@gmail.com',
            'password' => password_hash('admin1', PASSWORD_BCRYPT),
            'channels' => ['mail' => ['to' => 'jmcmah15@gmail.com']]
        ]);

        $user->save();

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
