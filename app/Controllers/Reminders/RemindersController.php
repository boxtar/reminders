<?php

namespace App\Controllers\Reminders;

use App\Controllers\Controller;
use Psr\Http\Message\{
    ServerRequestInterface as Request,
    ResponseInterface as Response
};

class RemindersController extends Controller
{
    /**
     * Render the home page
     *
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response $response
     */
    public function index(Request $request, Response $response, $args)
    {
        return $this->view->render($response, 'reminders/index.twig');
    }
}
