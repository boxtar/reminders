<?php

return function ($c) {

    // Flash Messages
    $c->set('flash', function () {
        return new Slim\Flash\Messages();
    });

    // Guzzle PHP Client
    $c->set('http', function () {
        return new GuzzleHttp\Client;
    });

    // Set Telegram as default notification channel
    $c->set(
        App\Contracts\Channel::class,
        DI\autowire(App\NotificationChannels\Telegram::class)
            ->method('settings', [
                'secret' => $c->get('settings')['telegram']['secret'],
                'chat_id' => $c->get('settings')['telegram']['chat_id']
            ])
    );

    // Set Email as the default notification channel
    // Uncomment to override the above
    // $c->set(
    //     App\Contracts\Channel::class,
    //     DI\autowire(App\NotificationChannels\Email::class)
    // );

    // Bind Mail Courier implementation
    $c->set(
        App\Contracts\Mail\Courier::class,
        function () use ($c) {
            $config = $c->get('settings')['mail'];
            return new App\Mail\SwiftMailer(
                $config['host'],
                $config['port'],
                $config['username'],
                $config['password'],
                $config['from']
            );
        }
    );

    // Bind Mail message implementation
    $c->set(
        App\Contracts\Mail\Message::class,
        DI\autowire(App\Mail\Message::class)
    );
};
