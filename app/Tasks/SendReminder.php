<?php

namespace App\Tasks;

use App\Services\Scheduler\Task;
use App\Domain\Reminders\Models\Reminder;
use App\Services\Notifications\Broadcaster;

class SendReminder extends Task
{
    /**
     * The instance that we're sending a reminder for (if it's due).
     * @var Reminder
     */
    protected $reminder;

    /**
     * The Broadcaster implementation that will handle the sending
     * of the message/reminder to the required notification channels.
     * @var Broadcaster
     */
    protected $broadcaster;

    public function __construct(Reminder $reminder, Broadcaster $broadcaster)
    {
        $this->reminder = $reminder;
        $this->broadcaster = $broadcaster;
    }

    /**
     * Required from parent abstract base class. This
     * does all the heavy lifting.
     */
    public function handle()
    {
        // Get the notification channels required for the given reminder.
        // Add a subject to the mail channel, if it exists.
        // Then pass them to the send function.
        $this->send(
            $this->addSubjectToChannelSettings(
                $this->getChannels()
            )
        );
        $this->postSend();
    }

    /**
     * Returns only the user channels that are required for this
     * particular reminder.
     * 
     * @return array
     */
    protected function getChannels()
    {
        return array_filter(
            $this->reminder->owner->channels,
            function ($channelKey) {
                return in_array($channelKey, $this->reminder->channels);
            },
            ARRAY_FILTER_USE_KEY
        );
    }

    /**
     * Builds up the body of the message to be sent
     * 
     * @return string
     */
    protected function buildMessage()
    {
        // Get recurrence string
        $recurrence = $this->reminder->isRecurring() ?
            "\n\n(Recurs {$this->reminder->frequency})" :
            "\n\nThis reminder won't recur";

        // Concat body and recurrence info
        return "{$this->reminder->body}{$recurrence}";
    }

    protected function addSubjectToChannelSettings($channels)
    {
        // Max length of subject excerpt
        $maxLength = 35;

        // Set subject to full body, initially
        $subject = $this->reminder->body;

        // If subject is greater than max length, cap it
        if (strlen($subject) > $maxLength) 
            $subject = substr($subject, 0, $maxLength) . "...";

        // Map over every channel and add the subject.
        // This is only used in the Mail channel (atm).
        return array_map(function ($channelSettings) use ($subject) {
            $channelSettings['subject'] = "Jabit: {$subject}";
            return $channelSettings;
        }, $channels);
    }

    /**
     * Ask the Broadcaster to send the reminder through the provided channels
     * 
     * @param array $channels Array of channels with key as valid id and value as array of settings
     */
    protected function send($channels)
    {
        $this->broadcaster
            ->message($this->buildMessage())
            ->settings($channels)
            ->send();
    }

    protected function postSend()
    {
        $this->log();
        $this->reminderMaintenance();
    }

    /**
     * Perform any reminder maintenance here.
     * e.g. updating next run date and time
     */
    protected function reminderMaintenance()
    {
        if (!$this->reminder->hasInitialReminderRun()) {
            $this->reminder->markInitialReminderComplete();
        }

        if ($this->reminder->isRecurring()) {
            $this->reminder->forwardNextRunDate();
        }

        return $this;
    }

    /**
     * Perform any necessary logging here
     */
    public function log()
    {
        return $this;
    }
}
