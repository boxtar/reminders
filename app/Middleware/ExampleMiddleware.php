<?php

namespace App\Middleware;

use Slim\Psr7\Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as Handler;

class ExampleMiddleware extends Middleware
{
    public function __invoke(Request $request, Handler $handler): Response
    {
        $response = $handler->handle($request);

        $message = "Example Middleware invoked";
        $exisitingBody = (string) $response->getBody();

        return $response;

        // If the response is a redirect, don't go messing with it.
        // This is similar to a paper house. There must be a better
        // way to deal with this type of thing...
        if ($response->hasHeader('Location')) {
            return $response;
        }
        // So request has been passed through any middleware to be called after this one.
        // We can now take this oppportunity to amend/obliterate the Response.
        $exisitingBody = (string) $response->getBody();
        $response = new Response;
        $response->getBody()->write($exisitingBody);
        return $response;
    }
}
