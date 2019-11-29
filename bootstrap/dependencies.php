<?php

use App\Services\Notifications\Broadcaster;

return function ($c) {

    // Flash Messages
    $c->set('flash', function () {
        return new Slim\Flash\Messages();
    });

    // Guzzle HTTP Client
    $c->set('http', function () {
        return new GuzzleHttp\Client;
    });

    // Notification Broadcaster. This just Proxies to Channel implementations
    // to do the hard work.
    $c->set('notifications.broadcaster', function () use ($c) {
        return new Broadcaster($c);
    });

    $c->set('notifications.email', DI\autowire(\App\Services\Notifications\Channels\Email::class));
    $c->set('notifications.telegram', DI\autowire(\App\Services\Notifications\Channels\Telegram::class));

    // Bind Mail Courier implementation
    $c->set(
        App\Services\Mail\Contracts\Courier::class,
        function () use ($c) {
            $config = $c->get('settings')['mail'];
            return new App\Services\Mail\SwiftMailer(
                $config['host'],
                $config['port'],
                $config['username'],
                $config['password']
            );
        }
    );

    // Bind Mail message implementation
    $c->set(
        App\Services\Mail\Contracts\Message::class,
        DI\autowire(App\Services\Mail\Message::class)
            ->method(
                'from',
                $c->get('settings')['mail']['from']
            )
            ->method(
                'subject',
                $c->get('settings')['mail']['subject']
            )
    );
};
