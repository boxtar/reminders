<?php

namespace App\Services\Notifications;

use App\Services\Notifications\Channels\Email;
use App\Services\Notifications\Channels\Telegram;

// Need to register Channel implementations with Notifications Manager. Can then use
// the registered channel keys to invoke a desired Notification Channel.

class Broadcaster
{
    /**
     * I do NOT like this. It ties this service to this implementation...
     * This is required to resolve the channel implementations. If I can find
     * a better way of registering the implementations then brilliant!
     * TODO: Think of a better way of solving this problem.
     */
    protected $container;

    /**
     * Array of available notification channels:
     * The key is the unique identifier for a channel.
     * The value is the full class name so that we can spin up new instances when needed.
     * 
     * The User may provide replacements or add in custom channel implementations.
     */
    protected $defaultChannels = [
        'mail' => 'notifications.email',
        'telegram' => 'notifications.telegram',
    ];

    protected $channels = [];

    // The message to be sent
    protected $message = '';

    // The settings for each channel the message is being sent through
    protected $settings = [];

    /**
     * @param array $channels Array of channels to be merged with, or replace, the defaults.
     */
    public function __construct($container)
    {
        $this->container = $container;
        $this->restoreDefaultChannels();
        return $this;
    }

    /**
     * 
     * @return array The currently configured channels
     */
    public function getChannels()
    {
        return $this->channels;
    }

    /**
     * 
     * @return array The default channels that come with this service
     */
    public function getDefaultChannels()
    {
        return $this->defaultChannels;
    }

    public function mergeChannels($channels)
    {
        $this->channels = array_merge(
            $this->getChannels(),
            $channels
        );
        return $this;
    }

    public function replaceChannels($channels)
    {
        $this->channels = $channels;
        return $this;
    }

    public function restoreDefaultChannels()
    {
        $this->replaceChannels(
            $this->getDefaultChannels()
        );
        return $this;
    }

    /**
     * @param string $message The message to be broadcast over the channels.
     */
    public function message(string $message)
    {
        $this->message = $message;
        return $this;
    }

    /**
     * @param array $channelSettings An array with valid channel id as key and the settings for each channel as the value.
     */
    public function settings($channelSettings)
    {
        $this->settings = $channelSettings;
        return $this;
    }

    /**
     * Loop over each of the required channels (settings)
     */
    public function send()
    {
        foreach ($this->settings as $channelKey => $channelSettings) {
            ($this->container->get($this->channels[$channelKey]))
                ->message($this->message)
                ->settings($channelSettings)
                ->send();
        }
    }
}
