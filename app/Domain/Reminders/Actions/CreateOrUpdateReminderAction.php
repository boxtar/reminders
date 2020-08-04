<?php

namespace App\Domain\Reminders\Actions;

use App\Response;
use App\Models\User;
use App\Domain\Reminders\ReminderData;
use App\Domain\Reminders\Models\Reminder;
use App\Domain\Recurrences\RecurrencesSupport;
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
        /** 
         * @todo: refactor away from this. Instead, have the code return a domain
         * specific response (UnauthorizedResponse, ValidationErrorsResponse, 
         * ReminderCreatedResponse, ReminderUpdateResponse...)
         */
        $response = Response::create();

        // This is only applicable for an Update.
        // Authorise update attempt
        /** @todo: change this so that it returns a UnauthorizedResponse object */
        if ($reminder && !$user->canUpdate($reminder)) {
            $response->setStatus(401); // Bad Request
            $response->setErrors(['message' => 'Unauthorized']); // Set Errors
            return $response;
        }

        // Create and execute validator using the new data.
        $validator = (new CreateOrUpdateReminderValidation($newData))->validate();

        // If there are validation errors, set them into response and return to caller.
        /** @todo: change this so that it returns a ValidationErrorsResponse object */
        if ($validator->fails()) {
            $response->setStatus(400); // Bad Request
            $response->setErrors($validator->getErrors()); // Set Errors
            return $response;
        }

        /**
         * If this is an update and the reminder date is not changing
         * then make sure we are keeping the initial reminder run
         * flag the same as it currently is.
         */
        if ($reminder && $reminder->next_run == $newData->next_run) {
            $newData->initial_reminder_run = $reminder->hasInitialReminderRun();
        }

        // Set/Update recurrence flag
        $newData->is_recurring = RecurrencesSupport::isFrequencyValid($newData->frequency);

        /** 
         * Create/Update the reminder (persists)
         * 
         * @todo: I really want this to be a Repository interaction.
         */
        $reminder = $reminder ?
            tap($reminder)->update($newData->toArray()) :
            $user->reminders()->create($newData->toArray());

        // Put the updated reminder into the response and return to caller.
        return $response->setData($reminder->toArray());
    }
}
