<?php

namespace App\NotificationChannels;

use App\Contracts\Channel;
use App\Contracts\Mail\Message;

class Email implements Channel
{

    protected $host;
    protected $port;
    protected $username;
    protected $password;

    // Mail Message instance
    protected $message;

    public function __construct(Message $message)
    {
        $this->message = $message;
        // This should be something like auth()->user()->channel('email')->email;
        $this->setRecipient("jmcmah15@gmail.com");
        // Set a default subject
        $this->setSubject("You have a Reminder");
    }

    public function setRecipient($recipient)
    {
        $this->message->to($recipient);
    }

    public function setSubject($subject)
    {
        $this->message->subject($subject);
    }

    public function settings($settings)
    {
        return $this;
    }

    public function message($message)
    {
        $this->message->html($message);
        return $this;
    }

    public function send()
    {
        $this->message->send();
    }
}
