<?php

namespace App\Services\Validation\Rules;

use Respect\Validation\Rules\AbstractRule;

class UniqueEmail extends AbstractRule {

    public function validate($input) {
        // This would ideally be a User Repository instead of a specific implementation
        return \App\Models\User::whereEmail($input)->count() === 0;
    }

}