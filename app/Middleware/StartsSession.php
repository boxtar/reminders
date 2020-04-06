<?php

namespace App\Middleware;

use App\Helpers\Utils;
use Slim\Psr7\Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as Handler;

class StartsSession extends Middleware
{
    public function __invoke(Request $request, Handler $handler): Response
    {
        // Lifetime of 1 day for session cookie.
        $lifetime = 60 * 60 * 24;
        // Start (which sends a cookie) or Restart a Session from the cookie.
        @session_start();
        // Manually set the cookie with a lifetime of 1 day and with HTTPOnly set to true.
        setcookie(session_name(), session_id(), time() + $lifetime, "/", "", true, true);
        // Continue through middleware stack.
        return $handler->handle($request);
    }
}
