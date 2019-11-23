<?php

namespace App\Controllers;

use App\Models\Reminder;
use App\Controllers\Controller;
use App\Helpers\Dates;
use App\Scheduler\FrequencyBuilder;
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
        $reminders = Reminder::latest()->get();
        return $this
            ->view()
            ->render($response, 'reminders/index.twig', compact('reminders'));
    }

    /**
     * Render the home page
     *
     * @param Request $request
     * @param Response $response
     * @param [type] $args
     * @return Response $response
     */
    public function store(Request $request, Response $response, $args)
    {
        // Grab the parsed request body and cast to a standard object
        $input = (object) $request->getParsedBody();

        // Validation
        if (!$this->validate($input)) {
            // Redirect
            return $this->redirect($response);
        }

        // Get the cron expression based on user input and check it is valid
        if (CronExpression::isValidExpression($expression = $this->buildCronExpression($input))) {
            $input->expression = $expression;
            $this->createReminder($input);
            $this->flash()->addMessage('messages', "Successfully added reminder '{$input->body}'");
        }

        // Redirect
        return $this->redirect($response);
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
        $reminder = Reminder::findOrFail($args['id']);

        $reminder->delete();

        $this->flash()->addMessage('messages', "{$reminder->body} has been archived");
        
        return $this->redirectToRoute($request, $response, 'reminders.index');
    }

    /**
     * Stores a new reminder using the $data passed in.
     * 
     * @param object|array $data - All the required data for creating a reminder.
     */
    protected function createReminder($data)
    {
        $data = (array) $data;
        Reminder::create($data);
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
    protected function validate($data)
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
