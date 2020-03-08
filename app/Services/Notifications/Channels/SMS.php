<?php

namespace App\Services\Notifications\Channels;

use Twilio\Rest\Client;
use App\Services\Notifications\Contracts\Channel;

class SMS implements Channel
{
    /**
     * This is coupled to Twilio!
     * TODO:
     * Make it based on an interface so that we could potentially swap out to another
     * SMS provider if required. Similiar to how Email channel is implemented.
     */

    /**
     * SID from Twilio - Must be set via settings
     */
    protected $sid;

    /**
     * Auth Token - Must be set via settings
     */
    protected $auth_token;

    /**
     * Twilio number that SMS to be sent from - Must be set via settings
     */
    protected $sms_from;

    /**
     * Recipient(s)
     */
    protected $to;

    /**
     * Message to be sent to Telegram chat.
     */
    protected $message;

    public function __construct()
    {
        return $this;
    }

    public function settings($settings)
    {
        if (array_key_exists('sid', $settings)) {
            $this->sid = $settings['sid'];
        }

        if (array_key_exists('auth_token', $settings)) {
            $this->auth_token = $settings['auth_token'];
        }

        if (array_key_exists('sms_from', $settings)) {
            $this->sms_from = $settings['sms_from'];
        }

        if (array_key_exists('number', $settings)) {
            $this->to = $settings['number'];
        }

        return $this;
    }

    public function message($message)
    {
        $this->message = $message;
        return $this;
    }

    /**
     * Send the message to the configured Telegram API
     */
    public function send()
    {
        if ($this->to) {
            $client = new Client($this->sid, $this->auth_token);
            $client->messages->create($this->to, [
                'from' => $this->sms_from,
                'body' => $this->message
            ]);
        }
    }
}
