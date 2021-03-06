<?php

namespace App\Services\Notifications;

// Need to register Channel implementations with Notifications Manager. Can then use
// the registered channel keys to invoke a desired Notification Channel.

class Broadcaster
{
    /**
     * I do NOT like this. It ties this service to this implementation...
     * This is required to resolve the channel implementations.
     * TODO: Find a general way of registering channel implementations with 
     * this Broadcaster.
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
        'sms' => 'notifications.sms'
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

    /**
     * Restores channels to the defaults.
     * TODO: Is this indirection really required? Think about what benefits
     * the method calls are providing over simply setting the channel prop
     * to the defaultChannel prop directly within this method... 
     */
    public function restoreDefaultChannels()
    {
        return $this->replaceChannels(
            $this->getDefaultChannels()
        );
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
     * Loop over each of the required channels (settings), resolve
     * an instance of the required channel out of the container, set
     * it up with message and necessary settings then send.
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
