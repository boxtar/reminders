<?php

namespace App\Controllers\Reminders;

use App\Controllers\Controller;
use App\Models\Reminder;
use Psr\Http\Message\{
    ServerRequestInterface as Request,
    ResponseInterface as Response
};

class ArchiveReminderController extends Controller
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
        // Get the currently authenticated user
        $user = $this->auth->user();

        // Find Reminder that user is trying to delete
        $reminder = Reminder::findOrFail($args['id']);

        // Authorise the delete attempt
        if (!$user->canUpdate($reminder)) {
            $this->json($response, ['message' => 'Unauthorized'], 401);
        }

        // If we get here then the user is authorised to delete the reminder
        $reminder->delete();

        // Respond
        return $this->json($response, ['message' => "{$reminder->body} has been archived", 'id' => $args['id']]);
    }
}
