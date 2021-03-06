<?php

namespace App\Controllers;

use Psr\Container\ContainerInterface;
use Respect\Validation\Validator as v;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Routing\RouteContext;

abstract class Controller
{
    /**
     * The container instance.
     *
     * @var ContainerInterface
     */
    protected $container;

    /**
     * Set up controllers to have access to the container.
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;

        // Tell Respect Validator where to find custom validation rules.
        v::with('App\\Services\\Validation\\Rules');
    }

    // Performs validation on the given input using the given rules.
    protected function validate($input, array $rules)
    {
        return $this
            ->container
            ->get('validator')
            ->validate($input, $rules);
    }

    // Returns instance of flash messaging service
    protected function flash($key = null, $message = null)
    {
        $flash = $this->container->get('flash');
        return empty($message) ? $flash : $flash->addMessage($key, $message);
    }

    protected function urlFor(Request $request, $routeName)
    {
        return $request
            ->getAttribute(RouteContext::ROUTE_PARSER)
            ->urlFor($routeName);
    }

    protected function json(Response $response, $data = [], $status = 200)
    {
        $response->getBody()->write(
            json_encode(['data' => (array) $data])
        );
        return $response->withHeader("Content-Type", "application/json")->withStatus($status);
    }

    protected function redirect(Response $response, $location = "/")
    {
        return $response->withHeader('Location', $location)->withStatus(302);
    }

    protected function redirectToRoute(Request $request, Response $response, $routeName)
    {
        return $this->redirect(
            $response,
            $this->urlFor($request, $routeName)
        );
    }

    public function __get($property)
    {
        return $this->container->get($property) ?? null;
    }
}
