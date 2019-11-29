<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// Temporarily storing this here for info:
// Create hashed password:
// dump(password_hash("admin1", PASSWORD_BCRYPT));

// Verify hashed password matches plain text password:
// dump(password_verify("admin1", $user->password));

class User extends Model
{
    protected $fillable = [
        'name', 'email', 'password', 'channels'
    ];

    protected $casts = [
        'channels' => 'array'
    ];

    public function reminders()
    {
        return $this->hasMany(Reminder::class);
    }
}
