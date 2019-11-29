<?php

namespace App\Services\Notifications\Contracts;

interface Channel {
    public function message($message);
    public function settings($settings);
    public function send();
}