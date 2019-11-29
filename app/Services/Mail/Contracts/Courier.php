<?php

namespace App\Services\Mail\Contracts;

interface Courier {
    public function send(Message $message);
}