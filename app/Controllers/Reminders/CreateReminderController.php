<?php

namespace App\Controllers\Reminders;

use App\Controllers\Controller;
use App\Domain\Reminders\ReminderData;
use App\Domain\Reminders\Actions\CreateOrUpdateReminderAction;
use Psr\Http\Message\{
    ServerRequestInterface as Request,
    ResponseInterface as Response
};

class CreateReminderController extends Controller
{
    /**
     * Returns the Authenticated User's Reminders as JSON
     *
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response $response
     */
    public function __invoke(Request $request, Response $response)
    {
        // Grab the authenticated user
        $user = $this->auth->user();

        // Process request
        $actionResponse = CreateOrUpdateReminderAction::create()->execute(new ReminderData($request->getParsedBody()), $user);

        // Deal with potential Validation/Action errors.
        if (count($errors = $actionResponse->getErrors())) {
            return $this->json($response, ['errors' => $errors], $actionResponse->getStatus());
        }

        // Success response.
        return $this->json($response, $actionResponse->getData());
    }
}
