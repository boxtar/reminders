<?php

namespace App\Middleware;

use Slim\Psr7\Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as Handler;

class AuthMiddleware extends Middleware
{
    /**
     * Where should guest users be redirected to when attempting
     * to access auth protected routes?
     */
    protected $redirectTo = '/login';

    public function __invoke(Request $request, Handler $handler): Response
    {
        $auth = $this->container->get('auth');

        // If request is not authenticated (i.e. is a guest), redirect.
        if ($auth->guest()) {
            return (new Response())
                ->withHeader('Location', $this->redirectTo)
                ->withStatus(302);
        }

        return $handler->handle($request);
    }
}
