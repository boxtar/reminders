<?php

namespace App\Domain\Reminders\Actions;

use App\Response;
use App\Models\User;
use App\Domain\Reminders\ReminderData;
use App\Domain\Reminders\Models\Reminder;
use App\Domain\Recurrences\RecurrenceBuilder;
use App\Domain\Recurrences\RecurrencesSupport;
use App\Domain\Reminders\ReminderExpressionBuilder;
use App\Domain\Reminders\Validators\CreateOrUpdateReminderValidation;

class CreateOrUpdateReminderAction
{
    public function __construct()
    {
        // 
    }

    /**
     * Quick factory (so user doesn't need to know how to build an instance)
     * @return self
     */
    public static function create()
    {
        return new self();
    }

    /**
     * @param ReminderData $newData
     * @param User $user
     * @param Reminder|null $reminder
     * @return Response
     */
    public function execute(ReminderData $newData, User $user, Reminder $reminder = null)
    {
        // Create the response that will be built in this action and returned to caller.
        $response = Response::create();

        // This is only applicable for an Update.
        // Authorise update attempt
        if ($reminder && !$user->canUpdate($reminder)) {
            $response->setStatus(401); // Bad Request
            $response->setErrors(['message' => 'Unauthorized']); // Set Errors
            return $response;
        }

        // Create and execute validator using the new data.
        $validator = (new CreateOrUpdateReminderValidation($newData))->validate();

        // If there are validation errors, set them into response and return to caller.
        if ($validator->fails()) {
            $response->setStatus(400); // Bad Request
            $response->setErrors($validator->getErrors()); // Set Errors
            return $response;
        }

        // Calculate new reminder expression (may not have changed)
        $newData->expression = (new ReminderExpressionBuilder($newData))->build();

        // This is only applicable for an Update.
        // If the original expression is the same as the new expression then it isn't being updated.
        // However, we also need to make sure that the original reminder hasn't actually run yet
        // before we change the initial_reminder_run flag to true.
        // Note: ReminderData defaults this flag to false.
        if ($reminder && $reminder->initial_reminder_run && $reminder->expression === $newData->expression) {
            $newData->initial_reminder_run = true;
        }

        // Update Frequency Expression
        $newData->is_recurring = RecurrencesSupport::isFrequencyValid($newData->frequency);
        $newData->recurrence_expression = (new RecurrenceBuilder($newData))->build();

        // Create/Update the reminder (persists) - I really want this to be a Repository interaction.
        $reminder = $reminder ?
            tap($reminder)->update($newData->toArray()) :
            $user->reminders()->create($newData->toArray());

        // Put the updated reminder into the response and return to caller.
        return $response->setData($reminder->toArray());
    }
}
