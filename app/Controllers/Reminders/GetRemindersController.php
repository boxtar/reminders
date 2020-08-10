<?php

namespace App\Controllers\Reminders;

use App\Controllers\Controller;
use Psr\Http\Message\{
    ServerRequestInterface as Request,
    ResponseInterface as Response
};

class GetRemindersController extends Controller
{
    /**
     * Returns the Authenticated User's Reminders as JSON
     *
     * @param Request $request
     * @param Response $response
     * @param [type] $args
     * @return Response $response
     */
    public function __invoke(Request $request, Response $response, $args)
    {
        $reminders = $this->auth->check() ?
            $this->auth->user()->reminders->sortBy('reminder_date')->toArray() :
            [];
        return $this->json($response, $reminders);
    }
}
