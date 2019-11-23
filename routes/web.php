<?php

use Psr\Http\Message\{
    ServerRequestInterface as Request,
    ResponseInterface as Response,
    ServerRequestInterface
};
use App\Controllers\RemindersController;


$app->get('/', function (ServerRequestInterface $request, Response $response) {
    return $response->withHeader('Location', $request->getAttribute('routeParser')->urlFor('reminders.index'));
});
$app->get('/reminders', RemindersController::class . ':index')->setName('reminders.index');
$app->post('/reminders', RemindersController::class . ':store')->setName('reminders.store');
$app->delete('/reminders/{id}', RemindersController::class . ':delete')->setName('reminders.delete');

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

// $app->get('/test', function (Request $request, Response $response) {
//     $reminder = Reminder::create([
//         'body' => 'This is an example reminder.',
//         'frequency' => 'monthly',
//         'day' => 1,
//         'date' => 1,
//         'time' => '10:00',
//         'expression' => '* * * * *',
//         'run_once' => true,
//     ]);

//     $response
//         ->getBody()
//         ->write(json_encode(['data' => $reminder]));

//     return $response
//         ->withHeader('Content-type', 'application/json');
// });
