<?php

return function ($c) {
    $c->set('settings', function () {
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

            'telegram' => [
                'chat_id' => getenv('TELEGRAM_CHAT_ID'),
                'secret' => getenv('TELEGRAM_SECRET')
            ]
        ];
    });
};
