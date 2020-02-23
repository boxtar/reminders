<?php

namespace App\Services\Validation\Exceptions;

use Respect\Validation\Exceptions\ValidationException;

class UniqueEmailException extends ValidationException
{
    public static $defaultTemplates = [
        self::MODE_DEFAULT => [
            self::STANDARD => 'This email address is already in use with Jabit Reminders.',
        ],
        self::MODE_NEGATIVE => [
            self::STANDARD => 'This email address cannot be found',
        ]
    ];
}
