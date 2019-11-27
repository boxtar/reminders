<?php

namespace App\Mail;

use App\Contracts\Mail\Courier;
use App\Contracts\Mail\Message;
use Swift_Mailer;
use Swift_Message;
use Swift_SmtpTransport;

class SwiftMailer implements Courier
{
    protected $host = null;
    protected $port = null;
    protected $username = null;
    protected $password = null;
    protected $from = [
        'address' => null,
        'name' => null
    ];

    public function __construct($host, $port, $username, $password, $from = [])
    {
        $this->host = $host;
        $this->port = $port;
        $this->username = $username;
        $this->password = $password;
        $this->from = array_merge($this->from, $from);
    }

    public function send(Message $message)
    {
        // Create the SMTP Transport
        $transport = (new Swift_SmtpTransport($this->host, $this->port))
            ->setUsername($this->username)
            ->setPassword($this->password);

        $mailer = new Swift_Mailer($transport);

        // Create a message
        $swiftMessage = (new Swift_Message())
            ->setSubject($message->getSubject())
            ->setFrom($this->from['address'], $this->from['name'])
            ->addTo($message->getRecipient())
            ->setBody($message->getBody())
            ->addPart($message->getHtml(), 'text/html');

        return $mailer->send($swiftMessage);
    }
}
