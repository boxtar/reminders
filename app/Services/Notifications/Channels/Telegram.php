<?php

namespace App\Services\Notifications\Channels;

use GuzzleHttp\Client;
use App\Services\Notifications\Contracts\Channel;

class Telegram implements Channel
{

    /**
     * Http client for submitting requests
     */
    protected $http;

    /**
     * Uri - configurable via settings
     */
    protected $uri = "https://api.telegram.org/bot";


    /**
     * Action - configurable via settings
     */
    protected $action = 'sendMessage';

    /**
     * Telegram secret - Must be set via settings
     */
    protected $secret;

    /**
     * Telegram chat id - Must be set via settings
     */
    protected $chatId;

    /**
     * Message to be sent to Telegram chat.
     */
    protected $message;

    public function __construct()
    {
        // Bad - Have this injected so that it can be mocked for testing in the future.
        $this->http = new Client;
        
        return $this;
    }

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
