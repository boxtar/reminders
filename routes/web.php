<?php

use App\Controllers\Auth\LoginController;
use App\Controllers\Auth\LogoutController;
use App\Controllers\Auth\RegisterController;
use Psr\Http\Message\{
    ServerRequestInterface as Request,
    ResponseInterface as Response,
};
use App\Controllers\RemindersController;
use App\Middleware\AuthMiddleware;
use App\Middleware\GuestMiddleware;
use Slim\Routing\RouteCollectorProxy;

$app->get('/test', function (Request $request, Response $response) { });

// Home page (redirect to Login for now)
$app->get('/', function (Request $request, Response $response) {
    return $response->withHeader(
        'Location',
        $request->getAttribute('routeParser')->urlFor('login.index')
    );
});

// Auth protected group
$app->group('/reminders', function (RouteCollectorProxy $group) {
    // Returns html showing all active reminders
    $group->get('', RemindersController::class . ':index')->setName('reminders.index');

    // Creates a new reminder
    $group->post('', RemindersController::class . ':store')->setName('reminders.store');

    // Deletes the given reminder
    $group->delete('/{id}', RemindersController::class . ':delete')->setName('reminders.delete');

    // Logout
    $group->post('/logout', LogoutController::class . ':store')->setName('logout.store');
})->add(new AuthMiddleware($app->getContainer()));

// Guest only routes
$app->group('', function (RouteCollectorProxy $group) {
    $group->get('/register', RegisterController::class . ':index')->setName('register.index');
    $group->post('/register', RegisterController::class . ':store')->setName('register.store');
    $group->get('/login', LoginController::class . ':index')->setName('login.index');
    $group->post('/login', LoginController::class . ':store')->setName('login.store');
})->add(new GuestMiddleware($app->getContainer()));

// Messing around with fake API
$app->get('/api/reminders', function ($req, $res) {
    $reminders = [
        ['body' => 'One'],
        ['body' => 'Two'],
        ['body' => 'Three'],
    ];
    $res->getBody()->write(json_encode($reminders));
    return $res->withHeader('Content-Type', 'application/json');
})->setName('api.reminders.index');
