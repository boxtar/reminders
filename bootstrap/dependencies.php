<?php

use Slim\Flash\Messages;

return function ($c) {

    // Flash Messages
    $c->set('flash', function () {
        return new Messages();
    });

    // Guzzle PHP Client
    $c->set('guzzle', function () {
        return new GuzzleHttp\Client;
    });
};
