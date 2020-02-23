<?php

namespace App\Controllers;

use App\Models\Reminder;
use App\Controllers\Controller;
use App\Helpers\Dates;
use App\Helpers\Utils;
use App\Models\User;
use App\Services\Scheduler\FrequencyBuilder;
use Cron\CronExpression;
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
     * @param [type] $args
     * @return Response $response
     */
    public function index(Request $request, Response $response, $args)
    {
        $reminders = $this->auth->check() ? $this->auth->user()->reminders : [];
        return $this
            ->view
            ->render($response, 'reminders/index.twig', compact('reminders'));
    }

    /**
     * Deal with resource storage request.
     *
     * @param Request $request
     * @param Response $response
     * @param [type] $args
     * @return Response $response
     */
    public function store(Request $request, Response $response, $args)
    {
        $user = $this->auth->user();

        // Grab the parsed request body and cast to a standard object
        $input = (object) $request->getParsedBody();

        // TODO: Let user configure from the frontend which channels to use when sending reminder.
        $input->channels = ["telegram", "mail"];

        // Validation
        if (!$this->customValidation($input)) {
            return $this->redirectToRoute($request, $response, 'reminders.index');
        }

        // Get the cron expression based on user input and check it is valid, then create reminder
        // and associate with the authenticated user.
        if (CronExpression::isValidExpression($expression = $this->buildCronExpression($input))) {
            $input->expression = $expression;
            $user->reminders()->create((array) $input);
            $this->flash()->addMessage('messages', "Successfully added reminder '{$input->body}'");
        }

        // Redirect
        return $this->redirectToRoute($request, $response, 'reminders.index');
    }

    /**
     * Delete a reminder
     * 
     * @param Request $request
     * @param Response $response
     * @param [type] $args
     * @return Response $response
     */
    public function delete(Request $request, Response $response, $args)
    {
        // Find Reminder that user is trying to delete
        $reminder = Reminder::findOrFail($args['id']);

        // Get the currently authenticated user
        $user = $this->auth->user();

        // Authorise the delete attempt
        if (!$user->canUpdate($reminder)) {
            $this->flash()->addMessage('errors', "Unauthorized");
            return $this->redirectToRoute($request, $response, 'reminders.index');
        }

        // If we get here the user is authorised to delete the reminder
        $reminder->delete();

        // Flash and redirect
        $this->flash()->addMessage('messages', "{$reminder->body} has been archived");
        return $this->redirectToRoute($request, $response, 'reminders.index');
    }

    /**
     * Builds up a cron expression based on the provided
     * date and time values.
     * 
     * @param object $input - Object will the required date and time values.
     */
    protected function buildCronExpression($input)
    {
        list($hour, $minute) = explode(":", $input->time);
        return (new FrequencyBuilder)
            ->frequency($input->frequency)
            ->day($input->day)
            ->date($input->date)
            ->time((int) $hour, (int) $minute)
            ->getExpression();
    }

    /**
     * Validate a store request (creating a new Reminder).
     * This mutates the provided $data.
     * 
     * @param object $data - Reminder data to be validated.
     */
    protected function customValidation($data)
    {
        $errors = [];

        // Body
        if (!$data->body)
            $errors['body'] = 'What\'s the reminder?';

        // Frequency
        if (!$data->frequency)
            $errors['frequency'] = 'You must provide a frequency.';

        // Day
        if (!Dates::isValidDay($data->day))
            $data->day = null;

        // Date
        if (!Dates::isValidDate($data->date))
            $data->date = null;

        // Time - TODO
        // if not a valid time then default to midnight

        // Runs once?
        $data->run_once = isset($data->run_once);

        // If there are any validation errors, add to session.
        if (count($errors)) {
            foreach ($errors as $field => $message) {
                $this->flash()->addMessage('errors', "{$field}: {$message}");
            }
            return false;
        }

        return true;
    }
}
