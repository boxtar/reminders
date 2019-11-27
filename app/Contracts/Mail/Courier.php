<?php

namespace App\Contracts\Mail;

interface Courier {
    public function send(Message $message);
}