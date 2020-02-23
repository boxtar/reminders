<?php

namespace App\Controllers\Auth;

use App\Controllers\Controller;
use Psr\Http\Message\{
    ServerRequestInterface as Request,
    ResponseInterface as Response
};

class LogoutController extends Controller
{
    public function store(Request $request, Response $response)
    {
        $this->auth->logout();
        return $this->redirectToRoute($request, $response, 'login.index');
    }
}