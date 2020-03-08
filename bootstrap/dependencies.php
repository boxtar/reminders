<?php

use App\Helpers\Utils;
use Slim\Csrf\Guard;
use Slim\Flash\Messages;
use App\Services\Auth\Auth;
use App\Services\Validation\Validator;
use App\Services\Notifications\Broadcaster;
use App\Services\Notifications\Channels\{
    Email,
    SMS,
    Telegram
};
use App\Services\Mail\{
    SwiftMailer,
    Message
};
use App\Services\Mail\Contracts\{
    Courier,
    Message as MessageContract
};
use function DI\{
    create,
    autowire
};

return function ($app) {

    // Quicker container ref
    $container = $app->getContainer();

    // Convenience reference for Authentication Service
    $container->set('auth', create(Auth::class));

    // Convenience reference for Request Validator Service.
    $container->set('validator', create(Validator::class));

    // Slim CSRF (add to container for ease of reference in Controllers)
    $container->set('csrf', function () use ($app) {
        return new Guard($app->getResponseFactory());
    });

    // Convenience reference to Flash service.
    $container->set('flash', create(Messages::class));

    // Convenience reference to http client service.
    $container->set('http', function () {
        return new GuzzleHttp\Client;
    });

    // Register the notifications broadcaster into the container.
    $container->set('notifications.broadcaster', function () use ($container) {
        return new Broadcaster($container);
    });

    // Register the Email Channel into the container.
    $container->set('notifications.email', autowire(Email::class));

    // Register the Telegram Channel into the container.
    $container->set('notifications.telegram', autowire(Telegram::class));

    // Register the SMS Channel into the container.
    $container->set(
        'notifications.sms',
        function() use ($container) {
            $config = $container->get('settings')['twilio'];
            return (new SMS())->settings([
                'sid' => $config['sid'],
                'auth_token' => $config['auth_token'],
                'sms_from' => $config['sms_from'],
            ]);
        }
    );

    // Register Mail Courier implementation into the container.
    $container->set(
        Courier::class,
        function () use ($container) {
            $config = $container->get('settings')['mail'];
            return new SwiftMailer(
                $config['host'],
                $config['port'],
                $config['username'],
                $config['password']
            );
        }
    );

    // Bind Mail Message Interface to an Implementation into the container.
    // Without this the container cannot inject a Mail Message instance into
    // the Email channel.
    $container->set(
        MessageContract::class,
        autowire(Message::class)
            ->method(
                'from',
                $container->get('settings')['mail']['from']
            )
            ->method(
                'subject',
                $container->get('settings')['mail']['subject']
            )
    );
};
