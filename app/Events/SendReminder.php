<?php

namespace App\Events;

use GuzzleHttp\Client;
use App\Scheduler\Task;
use App\Models\Reminder;

class SendReminder extends Task
{
    /**
     * The instance that we're sending a reminder for (if it's due).
     */
    protected $reminder;

    /**
     * Instance of the HTTP client used to contacting API services.
     * (GuzzleHttp)
     */
    protected $client;

    /**
     * Settings for contacting API services.
     */
    protected $settings;

    /**
     * Telegram URI
     */
    protected $telegramUri = "https://api.telegram.org/bot";

    public function __construct(Reminder $reminder, Client $client, array $settings)
    {
        $this->reminder = $reminder;
        $this->client = $client;
        $this->settings = $settings;
        $this->telegramUri .= $this->settings['secret'] . '/sendMessage';
    }

    /**
     * This is called when the Task is due. Send reminder(s) out
     */
    public function handle()
    {
        // Hit the base URI for sending the reminder.
        $this->client->get($this->telegramUri, [
            'query' => [
                'chat_id' => $this->settings['chat_id'],
                'text' => $this->reminder->body
            ]
        ]);

        // TODO: Log this somewhere...
        dump("Reminder sent: {$this->reminder->id}");

        // Delete reminder if it should only be run once.
        if ($this->reminder->shouldRunOnce()) {
            $this->reminder->delete();
            // TODO: Log this somewhere...
            dump("Reminder archived as should only be run once: {$this->reminder->id}");
        }
    }
}
