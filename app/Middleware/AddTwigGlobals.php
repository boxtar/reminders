<?php

namespace App\Middleware;

use App\Domain\Recurrences\RecurrencesSupport;
use App\Services\Auth\Auth;
use Psr\Container\ContainerInterface;
use Slim\Views\Twig;
use Slim\Csrf\Guard;
use Slim\Psr7\Response;
use Slim\Flash\Messages as SessionFlash;
use Psr\Http\Server\RequestHandlerInterface as Handler;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * Puts the user request body for this session into the session global
 * and makes it available to twig globally.
 */
class AddTwigGlobals extends Middleware
{
    /**
     * Twig instance
     */
    protected $twig;

    /**
     * Slim Flash Message - Grab flashed messages and add to twig as global helpers.
     */
    protected $sessionFlash;

    /**
     * Csrf Guard: Used to add csrf name and token to twig as a global helper.
     */
    protected $csrf;

    /**
     * Authentication service: Provides access to auth checks and the authenticated user.
     */
    protected $auth;

    /**
     * Tied entirely to Twig and Slim Flash packages for now...
     */
    public function __construct(Twig $twig, ContainerInterface $container)
    {
        $this->twig = $twig;
        $this->container = $container;
    }

    protected function init() {
        $this->auth = $this->container->get('auth');
        $this->sessionFlash = $this->container->get('flash');
        $this->csrf = $this->container->get('csrf');
    }

    public function __invoke(Request $request, Handler $handler): Response
    {
        $this->init();

        $this->addAuthHelpers();

        $this->addCsrfHelpers();

        // Add any flashed messages from the previous session into twig as a global
        $this->twig
            ->getEnvironment()
            ->addGlobal('messages', $this->sessionFlash->getMessage('messages'));

        // Add any flashed errors from previous session into twig as a global
        $this->twig
            ->getEnvironment()
            ->addGlobal('errors', $this->sessionFlash->getMessage('errors'));

        // Make the Dates helper available to all views
        $this->twig
            ->getEnvironment()
            ->addGlobal('dates', new \App\Helpers\Dates);

        // Make the available recurrence frequencies available to all views
        $this->twig
            ->getEnvironment()
            ->addGlobal('frequencies', RecurrencesSupport::frequencies());

        // Set the old input from the previous session (cached in session)
        $this->twig
            ->getEnvironment()
            ->addGlobal('old', $this->sessionFlash->getMessage('old'));

        // Now set the input from this session as the old input for the next request from this session.
        $this->sessionFlash->addMessage(
            'old',
            array_merge(
                $request->getQueryParams(),
                (array) $request->getParsedBody()
            )
        );

        return $handler->handle($request);
    }

    // Makes csrf fields and data available in templates.
    protected function addCsrfHelpers()
    {
        $this->twig
            ->getEnvironment()
            ->addGlobal('csrf', [
                'html' => trim('
                    <input type="hidden" name="' . $this->csrf->getTokenNameKey() . '"
                        value="' . $this->csrf->getTokenName() . '">
                    <input type="hidden" name="' . $this->csrf->getTokenValueKey() . '"
                        value="' . $this->csrf->getTokenValue() . '">
                '),
                'data' => [
                    'name' => [
                        'key' => $this->csrf->getTokenNameKey(),
                        'value' => $this->csrf->getTokenName()
                    ],
                    'token' => [
                        'key' => $this->csrf->getTokenValueKey(),
                        'value' => $this->csrf->getTokenValue()
                    ]
                ]
            ]);
    }

    // Makes the Auth instance available inside templates
    protected function addAuthHelpers()
    {
        $this->twig
            ->getEnvironment()
            ->addGlobal('auth', $this->auth);
    }
}
