<?php

namespace App\Controllers\Reminders;

use App\Controllers\Controller;
use App\Domain\Reminders\ReminderData;
use App\Domain\Reminders\Models\Reminder;
use App\Domain\Reminders\Actions\CreateOrUpdateReminderAction;
use Psr\Http\Message\{
    ServerRequestInterface as Request,
    ResponseInterface as Response
};

class UpdateReminderController extends Controller
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

        // Find Reminder that user is trying to update
        $reminder = Reminder::findOrFail($args['id']);

        // If we get here then the user is authorised to update the reminder.
        $actionResponse = CreateOrUpdateReminderAction::create()->execute(new ReminderData($request->getParsedBody()), $user, $reminder);

        // Deal with potential Validation/Action errors.
        if(count($errors = $actionResponse->getErrors())) {
            return $this->json($response, ['errors' => $errors], $actionResponse->getStatus());
        }
        
        // Success response.
        return $this->json($response, $actionResponse->getData());
    }
}
