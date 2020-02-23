<?php

namespace App\Tasks;

use App\Models\Reminder;
use App\Services\Scheduler\Task;
use App\Services\Notifications\Broadcaster;

class SendReminder extends Task
{
    /**
     * The instance that we're sending a reminder for (if it's due).
     */
    protected $reminder;

    /**
     * The Broadcaster implementation that will handle the sending
     * of the message/reminder to the required notification channels.
     * 
     * TODO: Think about whether a new Broadcaster instance is needed
     * for each reminder or if we can pass in a Singleton instance.
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
        // Then pass them to the send function.
        $this->send(
            $this->getChannels()
        );
        $this->logReminderSent();
        $this->reminderMaintenance();
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
     * Ask Mr. Notification Manager to do its thing.
     * 
     * @param array $channels Array of channels with key as valid id and value as array of settings
     */
    protected function send($channels)
    {
        $this->broadcaster
            ->message($this->reminder->body)
            ->settings($channels)
            ->send();
    }

    protected function reminderMaintenance()
    {
        // Delete reminder if it should only be run once.
        if ($this->reminder->shouldRunOnce()) {
            $this->reminder->delete();
            $this->logReminderDeleted();
        }
    }

    public function logReminderSent()
    {
        return $this;
    }

    public function logReminderDeleted()
    {
        return $this;
    }
}
