<?php

namespace App\Services\Mail;

use Swift_Mailer;
use Swift_Message;
use Swift_SmtpTransport;
use App\Services\Mail\Contracts\Message;

class SwiftMailer implements \App\Services\Mail\Contracts\Courier
{
    protected $host = null;
    protected $port = null;
    protected $username = null;
    protected $password = null;

    public function __construct($host, $port, $username, $password)
    {
        $this->host = $host;
        $this->port = $port;
        $this->username = $username;
        $this->password = $password;
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
            ->setFrom(
                ...$this->formatFromFields(
                    $message->getFrom()
                )
            )
            ->addTo($message->getRecipient())
            ->setBody($message->getBody())
            ->addPart($message->getHtml(), 'text/html');

        return $mailer->send($swiftMessage);
    }

    protected function formatFromFields($fromFields)
    {
        return [
            $fromFields['address'],
            $fromFields['name'],
        ];
    }
}
