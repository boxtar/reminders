<?php

return function ($app) {
    
    $container = $app->getContainer();

    $container->set('settings', function () {
        return [
            'displayErrorDetails' => getenv('APP_DEBUG') === 'true',

            'app' => [
                'name' => getenv('APP_NAME')
            ],

            'views' => [
                'cache' => getenv('VIEW_CACHE_DISABLED') === 'true' ? false : __DIR__ . '/../storage/views'
            ],

            'db' => [
                'driver' => getenv('DB_DRIVER'),
                'host' => getenv('DB_HOST'),
                'database' => getenv('DB_DATABASE'),
                'username' => getenv('DB_USER'),
                'password' => getenv('DB_PASSWORD'),
                'charset' => 'utf8',
                'collation' => 'utf8_unicode_ci',
                'prefix' => ''
            ],

            'mail' => [
                'driver' => getenv('MAIL_DRIVER') ?: 'smtp',
                'host' => getenv('MAIL_HOST') ?: 'localhost',
                'port' => getenv('MAIL_PORT') ?: 25,
                'encryption' => getenv('MAIL_ENCRYPTION'),
                'username' => getenv('MAIL_USERNAME'),
                'password' => getenv('MAIL_PASSWORD'),
                'from' => [
                    'address' => getenv('MAIL_FROM_ADDRESS') ?: 'hello@example.com',
                    'name' => getenv('MAIL_FROM_NAME') ?: 'Example'
                ],
                'subject' => getenv('MAIL_SUBJECT') ?: getenv('APP_NAME') ?: '',
            ],
        ];
    });
};
