<?php

use App\Controllers\Auth\LoginController;
use App\Controllers\Auth\LogoutController;
use App\Controllers\Auth\RegisterController;
use App\Controllers\Reminders\ArchiveReminderController;
use App\Controllers\Reminders\CreateReminderController;
use App\Controllers\Reminders\GetRemindersController;
use Psr\Http\Message\{
    ServerRequestInterface as Request,
    ResponseInterface as Response,
};
use App\Controllers\Reminders\RemindersController;
use App\Middleware\AuthMiddleware;
use App\Middleware\GuestMiddleware;
use Slim\Routing\RouteCollectorProxy;
use Slim\Routing\RouteContext;

// $app->get('/test', function (Request $request, Response $response) {
//     $response->getBody()->write("Hello from test");
//     return $response->withHeader('Content-Type', 'application/json');
// });

// Home page (redirect to Login for now)
$app->get('/', function (Request $request, Response $response) {
    return $response->withHeader(
        'Location',
        $request->getAttribute(RouteContext::ROUTE_PARSER)->urlFor('login.index')
    );
});

// Auth protected non-API group.
$app->group('/reminders', function (RouteCollectorProxy $group) {
    // Returns base app for viewing and managing reminders
    $group->get('', RemindersController::class . ':index')->setName('reminders.index');
    // Logout
    $group->post('/logout', LogoutController::class . ':store')->setName('logout.store');
})->add(new AuthMiddleware($app->getContainer()));

// Auth protected API group.
$app->group('/api', function (RouteCollectorProxy $group) {
    // Returns JSON list of reminders for authenticated user.
    $group->get('/reminders', GetRemindersController::class)->setName('api.reminders.get');
    // Creates a new reminder.
    $group->post('/reminders', CreateReminderController::class)->setName('api.reminders.store');
    // Archives the reminder with the provided id.
    $group->delete('/reminders/{id}', ArchiveReminderController::class)->setName('api.reminders.archive');
})->add(new AuthMiddleware($app->getContainer()));

// Guest only routes
$app->group('', function (RouteCollectorProxy $group) {
    $group->get('/register', RegisterController::class . ':index')->setName('register.index');
    $group->post('/register', RegisterController::class . ':store')->setName('register.store');
    $group->get('/login', LoginController::class . ':index')->setName('login.index');
    $group->post('/login', LoginController::class . ':store')->setName('login.store');
})->add(new GuestMiddleware($app->getContainer()));

