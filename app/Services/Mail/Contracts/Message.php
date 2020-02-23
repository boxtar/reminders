<?php

namespace App\Services\Mail\Contracts;

interface Message {
    public function to(string $recipient);
    public function getRecipient();

    public function from(array $from);
    public function getFrom();

    public function subject(string $subject);
    public function getSubject();
    
    public function body(string $body);
    public function getBody();

    public function html(string $html);
    public function getHtml();
    
    public function send(Courier $courier = null);
}