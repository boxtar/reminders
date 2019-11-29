<?php

namespace App\Services\Mail;

use Exception;
use App\Services\Mail\Contracts\Courier;

class Message implements \App\Services\Mail\Contracts\Message
{
    protected $courier;

    /**
     * Recipients of this mail message
     */
    protected $recipient;

    /**
     * Who is the Email from? Will default to settings in config.
     */
    protected $from = [
        'name' => null,
        'address' => null,
    ];

    /**
     * Subject line of the mail message
     */
    protected $subject;

    /**
     * Body of the mail message (plain and html)
     */
    protected $body;

    /**
     * HTML version of the body
     */
    protected $html;

    public function __construct(Courier $courier)
    {
        $this->courier = $courier;
        return $this;
    }

    /**
     * Add recipient(s)
     * 
     * @param string|array
     */
    public function to($recipient)
    {
        $this->recipient = $recipient;
        return $this;
    }

    public function from($from)
    {
        $this->from = array_merge($this->from, $from);
        return $this;
    }

    /**
     * Set the mail's subject line
     * 
     * @param string
     */
    public function subject($subject)
    {
        $this->subject = $subject;
        return $this;
    }

    /**
     * Set the mail's body
     * 
     * @param string
     */
    public function body($body)
    {
        $this->body = $body;

        if (!$this->html)
            $this->html = $body;

        return $this;
    }

    /**
     * Set the mail's html body
     * 
     * @param string
     */
    public function html($html)
    {
        $this->html = $html;

        if (!$this->body)
            $this->body = $html;

        return $this;
    }

    /**
     * Returns the recipient of the mail message
     * 
     * @return string
     */
    public function getRecipient()
    {
        return $this->recipient;
    }

    public function getFrom()
    {
        return $this->from;
    }

    /**
     * Returns the mail's subject line
     * 
     * @return string
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * Returns the mail's body
     * 
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * Returns the mail's html body
     * 
     * @return string
     */
    public function getHtml()
    {
        return $this->html;
    }

    /**
     * Sends the mail message
     */
    public function send(Courier $courier = null)
    {
        if ($courier)
            return $courier->send($this);

        if ($this->courier)
            return $this->courier->send($this);

        throw new Exception("Cannot send Mail without a Courier");
    }
}
