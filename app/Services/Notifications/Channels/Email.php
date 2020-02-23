<?php

namespace App\Services\Notifications\Channels;

use App\Helpers\Utils;
use App\Services\Mail\Contracts\Message;
use App\Services\Notifications\Contracts\Channel;

/**
 * This channel uses the Mail service/package which is a simple class
 * that wraps up the required fields for an Email Message and passes
 * it over to a Courier implementation for sending.
 */
class Email implements Channel
{
    // All channels are configurable via their settings array.
    // The important setting for this is the 'to' field.
    protected $settings = [];

    // Mail Message instance
    protected $message;

    /**
     * Requires an implementation of the Message interface.
     * This should be auto-injected by a service container.
     */
    public function __construct(Message $message)
    {
        $this->message = $message;
    }

    /**
     * Required by Contract.
     * 
     * @param string
     */
    public function message($message)
    {
        $this->message->html($message);
        return $this;
    }

    /**
     * Required by Contract.
     * 
     * @param array
     */
    public function settings($settings)
    {
        if (array_key_exists('to', $settings)) {
            $this->message->to($settings['to']);
        }

        if (array_key_exists('subject', $settings)) {
            $this->message->subject($settings['subject']);
        }

        if (array_key_exists('from', $settings)) {
            $this->message->from($settings['from']);
        }

        return $this;
    }

    /**
     * Required by Contract.
     */
    public function send()
    {
        $this->message->send();
    }
}
