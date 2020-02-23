<?php

use DI\Container;
use Dotenv\Dotenv;
use Slim\Factory\AppFactory;

require_once __DIR__ . '/../vendor/autoload.php';

// Load environment variables from .env file.
(new Dotenv(__DIR__ . '/../'))->load();

// Create Container
$container = new Container();
AppFactory::setContainer($container);

// Create App
$app = AppFactory::create();

// Bind settings into container
(require __DIR__ . '/settings.php')($app);

// Bind dependencies into container
(require __DIR__ . '/dependencies.php')($app);

// Add Middlewares
(require __DIR__ . '/middlewares.php')($app);

/**
 * Setup Laravel Eloquent
 * TODO: Move this into Dependencies?
 */
$capsule = new \Illuminate\Database\Capsule\Manager;
$capsule->addConnection($container->get('settings')['db']);
$capsule->setAsGlobal();
$capsule->bootEloquent();

require_once __DIR__ . '/../routes/web.php';

return $app;
