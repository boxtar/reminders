<?php

namespace App\Middleware;

use Slim\Psr7\Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as Handler;

class GuestMiddleware extends Middleware
{
    /**
     * Where should guest users be redirected to when attempting
     * to access auth protected routes?
     */
    protected $redirectTo = '/reminders';

    public function __invoke(Request $request, Handler $handler): Response
    {
        $auth = $this->container->get('auth');

        // If request is authenticated (user is logged in), redirect away.
        if ($auth->check()) {
            return (new Response())
                ->withHeader('Location', $this->redirectTo)
                ->withStatus(302);
        }

        return $handler->handle($request);
    }
}
