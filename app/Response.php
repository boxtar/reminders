<?php

/**
 * Just a basic Application level Response to help with communication between
 * Domain and Interface (Controller) layers.
 */

namespace App;

class Response
{
    /**
     * @var number - HTTP Code status
     */
    protected $status = 200;

    /**
     * @var array - Response data
     */
    protected $data = [];

    /**
     * @var array - Errors
     */
    protected $errors = [];
    
    public function __construct()
    {
        // 
    }

    public static function create()
    {
        return new self;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param array $data
     * @return self
     */
    public function setData($data)
    {
        $this->data = $data;
        return $this;
    }

    /**
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }
    
    /**
     * @param array $errors
     * @return self
     */
    public function setErrors($errors)
    {
        $this->errors = $errors;
        return $this;
    }

    /**
     * Returns the HTTP status code
     * 
     * @return number
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set the HTTP status code
     * 
     * @param number $status
     * @return self
     */
    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }
}
