<?php

use Psr\Http\Message\{
    ServerRequestInterface as Request,
    ResponseInterface as Response,
};
use App\Controllers\RemindersController;

// class Mail
// {
//     public $message;
//     public $settings = [];

//     public function message($message)
//     {
//         $this->message = $message;
//         return $this;
//     }

//     public function settings($settings)
//     {
//         $this->settings = $settings;
//         return $this;
//     }
// }

// class Telegram
// {
//     public $message;
//     public $settings = [];

//     public function message($message)
//     {
//         $this->message = $message;
//         return $this;
//     }

//     public function settings($settings)
//     {
//         $this->settings = $settings;
//         return $this;
//     }
// }


$app->get('/test', function (Request $request, Response $response) {
    

    die();
});

// Redirects to /reminders
$app->get('/', function (ServerRequestInterface $request, Response $response) {
    return $response->withHeader('Location', $request->getAttribute('routeParser')->urlFor('reminders.index'));
});

// Returns html showing all active reminders
$app->get('/reminders', RemindersController::class . ':index')->setName('reminders.index');

// Creates a new reminder
$app->post('/reminders', RemindersController::class . ':store')->setName('reminders.store');

// Deletes the given reminder
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
