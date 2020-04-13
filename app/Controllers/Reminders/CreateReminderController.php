<?php

namespace App\Controllers\Reminders;

use App\Controllers\Controller;
use App\Domain\Reminders\ReminderData;
use App\Domain\Recurrences\RecurrenceBuilder;
use App\Domain\Recurrences\RecurrencesSupport;
use App\Domain\Reminders\ReminderFrequencyBuilder;
use App\Domain\Reminders\Rules\CreateReminderValidation;
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
        // IDEA: Perhaps the validations and creation of expressions below
        // can be turned into a Domain Action. Most of the actions below could
        // be encapsulated in a CreateReminderAction?
        // Possible issues is external dependencies.

        // Grab the authenticated user
        $user = $this->auth->user();

        // Extract Data from Request
        $data = new ReminderData($request->getParsedBody());

        // Validate Request
        $validator = (new CreateReminderValidation($data))->validate();
        if ($validator->fails()) {
            return $this->json($response, ['errors' => $validator->getErrors()], 422);
        }

        // Create initial reminder expression.
        $data->expression = (new ReminderFrequencyBuilder($data))->build();

        // Recurrence expression.
        $data->is_recurring = RecurrencesSupport::isFrequencyValid($data->frequency);
        $data->recurrence_expression = (new RecurrenceBuilder($data, $data->frequency))->build();
        
        // Create reminder for user
        $reminder = $user->reminders()->create($data->toArray());

        // Respond
        return $this->json($response, $reminder->toArray());
    }

    /**
     * Returns true if reminder is to recur, false otherwise.
     * 
     * @param string
     * @return bool
     */
    protected function isRecurring($frequency)
    {
        return $frequency !== "none";
    }
}
