<?php

namespace App\Services\Mail\Contracts;

interface Message {
    public function to(string $recipient);
    public function from(array $from);
    public function subject(string $subject);
    public function body(string $body);
    public function send(Courier $courier = null);
}