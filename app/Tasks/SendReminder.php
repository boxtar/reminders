<?php

namespace App\Tasks;

use App\Contracts\Channel;
use App\Scheduler\Task;
use App\Models\Reminder;

class SendReminder extends Task
{
    /**
     * The instance that we're sending a reminder for (if it's due).
     */
    protected $reminder;

    protected $channels = [];

    public function __construct(Reminder $reminder, Channel $defaultChannel)
    {
        $this->reminder = $reminder;
        $this->channels[] = $defaultChannel;
    }

    /**
     * This is called when the Task is due. Send reminder(s) out
     */
    public function handle()
    {        
        // Hit the notification channels
        foreach ($this->channels as $channel) {
            $channel->message($this->reminder->body)->send();
        }

        // TODO: Create a log table to log this action
        dump("Reminder sent: {$this->reminder->body}");

        // Delete reminder if it should only be run once.
        if ($this->reminder->shouldRunOnce()) {
            $this->reminder->delete();
            // TODO: Also need to log this action
            dump("Reminder archived as should only be run once: {$this->reminder->id}");
        }
    }
}
