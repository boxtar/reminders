<?php

namespace App\Controllers;

use DI\Container;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

abstract class Controller
{
    /**
     * The container instance.
     *
     * @var \Interop\Container\ContainerInterface
     */
    protected $c;

    /**
     * Set up controllers to have access to the container.
     *
     * @param \Interop\Container\ContainerInterface $container
     */
    public function __construct(Container $container)
    {
        $this->c = $container;
    }

    protected function urlFor(Request $request, $routeName)
    {
        return $request
            ->getAttribute('routeParser')
            ->urlFor($routeName);
    }

    protected function redirect(Response $response, $location = "/")
    {
        return $response->withHeader('Location', $location);
    }

    protected function redirectToRoute(Request $request, Response $response, $routeName)
    {
        return $this->redirect(
            $response,
            $this->urlFor($request, $routeName)
        );
    }

    // Returns instance of template renderer
    protected function view()
    {
        return $this->c->get('view');
    }

    // Returns instance of flash messaging service
    protected function flash()
    {
        return $this->c->get('flash');
    }
}
