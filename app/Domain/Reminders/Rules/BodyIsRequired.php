<?php

declare(strict_types=1);

namespace App\Domain\Reminders\Rules;

class BodyIsRequired extends BaseRule
{
    public function validate(): BaseRule
    {
        // Body
        if (!$this->_data->body) {
            $this->_errors['body'] = 'You must provide a body';
        }
        
        if (!count($this->_errors)) {
            $this->_passes = true;
        }
        
        return $this;
    }
}
