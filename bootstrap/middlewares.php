<?php

use App\Middleware\AddTwigGlobals;
use App\Middleware\StartsSession;
use Slim\Middleware\MethodOverrideMiddleware;
use Slim\Views\{
    Twig,
    TwigMiddleware
};

return function ($app) {

    // ----- Start Twig Setup -----
    $twig = new Twig(__DIR__ . '/../resources/views', [
        'cache' => $app->getContainer()->get('settings')['views']['cache']
    ]);
    $twigMiddleware = new TwigMiddleware(
        $twig,
        $app->getContainer(),
        $app->getRouteCollector()->getRouteParser()
    );
    $app->add($twigMiddleware);

    // Sets a bunch of globals on twig so they can be used in templates.
    $app->add(new AddTwigGlobals(
        $twig,
        $app->getContainer()
    ));
    // ----- End Twig Setup -----

    // Applies the Slim Csrf Guard middleware (if not running test...)
    if (getenv('APP_ENV') !== 'testing') {
        $app->add('csrf');
    }

    // Applies some handy route info to the $request attributes
    $app->addRoutingMiddleware();

    // Method overriding (put, patch, delete)
    $app->add(new MethodOverrideMiddleware);

    // Body parsing Middleware is required for JSON/XML
    $app->addBodyParsingMiddleware();

    // Slim error handling
    $app->addErrorMiddleware(true, true, true);

    // House for the php session initialisation
    $app->add(new StartsSession);
};
