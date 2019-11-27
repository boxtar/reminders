<?php

namespace App\Contracts\Mail;

interface Message {
    public function to(string $recipient);
    public function subject(string $subject);
    public function body(string $body);
    public function send(Courier $courier = null);
}