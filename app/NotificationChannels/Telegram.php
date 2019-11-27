<?php

namespace App\NotificationChannels;

use App\Contracts\Channel;
use GuzzleHttp\Client;

class Telegram implements Channel
{

    /**
     * Http client for submitting requests
     */
    protected $http;

    /**
     * Uri - configurable via setOptions
     */
    protected $uri = "https://api.telegram.org/bot";


    /**
     * Action - configurable via setOptions
     */
    protected $action = 'sendMessage';

    /**
     * Telegram secret - Must be set via setCredentials
     */
    protected $secret;

    /**
     * Telegram chat id - Must be set via setCredentials
     */
    protected $chatId;

    /**
     * Message to be sent to Telegram chat.
     */
    protected $message;

    public function __construct()
    {
        $this->http = new Client;

        // When auth is implemented set credentials here:
        // $credentials = auth()->user()->channel('telegram')
        // $credentials->secret;
        // $credentials->chat_id;
        
        return $this;
    }

    // public function setCredentials($credentials)
    // {
    //     if (array_key_exists('secret', $credentials)) {
    //         $this->secret = $credentials['secret'];
    //     }

    //     if (array_key_exists('chat_id', $credentials)) {
    //         $this->chatId = $credentials['chat_id'];
    //     }
    //     return $this;
    // }

    public function settings($settings)
    {
        if (array_key_exists('secret', $settings)) {
            $this->secret = $settings['secret'];
        }

        if (array_key_exists('chat_id', $settings)) {
            $this->chatId = $settings['chat_id'];
        }

        if (array_key_exists('uri', $settings)) {
            $this->uri = $settings['uri'];
        }

        if (array_key_exists('action', $settings)) {
            $this->action = $settings['action'];
        }
        return $this;
    }

    public function message($message)
    {
        $this->message = $message;
        return $this;
    }

    /**
     * Send the message to the configured Telegram API
     */
    public function send()
    {
        $uri = "{$this->uri}{$this->secret}/{$this->action}";

        return $this->http->get($uri, [
            'query' => [
                'chat_id' => $this->chatId,
                'text' => $this->message
            ]
        ]);
    }
}
