<?php
// I would rather do this in a middleware
session_start();

use Slim\Middleware\MethodOverrideMiddleware;

require_once __DIR__ . '/../vendor/autoload.php';

// Grab environment variables from .env
(new Dotenv\Dotenv(__DIR__ . '/../'))->load();

// Create new container
$container = new DI\Container();

// Instruct app factory to use the provided container
Slim\Factory\AppFactory::setContainer($container);

$app = Slim\Factory\AppFactory::create();

// Bind settings into container
(require __DIR__ . '/bindSettings.php')($container);

// Bind other dependencies into container
(require __DIR__ . '/dependencies.php')($container);

// Applies some handy route info to the $request attributes
$app->addRoutingMiddleware();

// Method overriding (put, patch, delete)
$app->add(new MethodOverrideMiddleware);

/**
 * Setup Twig View Templating Engine
 */
$twig = new Slim\Views\Twig(__DIR__ . '/../resources/views', [
    'cache' => $container->get('settings')['views']['cache']
]);

// Make the Dates helper available to all views
$twig->getEnvironment()->addGlobal('dates', new \App\Helpers\Dates);
// Make session messages available to all views
$twig->getEnvironment()->addGlobal('messages', $container->get('flash')->getMessage('messages'));
// Make session errors available to all views
$twig->getEnvironment()->addGlobal('errors', $container->get('flash')->getMessage('errors'));

$twigMiddleware = new Slim\Views\TwigMiddleware(
    $twig,
    $container,
    $app->getRouteCollector()->getRouteParser()
);

$app->add($twigMiddleware);

/**
 * Setup Laravel Eloquent
 */
$capsule = new \Illuminate\Database\Capsule\Manager;
$capsule->addConnection($container->get('settings')['db']);
$capsule->setAsGlobal();
$capsule->bootEloquent();

$errorMiddleware = $app->addErrorMiddleware(true, true, true);

require_once __DIR__ . '/../routes/web.php';

return $app;
