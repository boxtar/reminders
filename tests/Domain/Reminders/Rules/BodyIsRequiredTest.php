<?php

namespace Tests\Domain\Reminders\Rules;

use App\Domain\Reminders\Rules\BodyIsRequired;
use Tests\TestCase;

class BodyIsRequiredTest extends TestCase
{
    /** @test */
    public function passes_when_body_is_not_empty()
    {
        $data = $this->makeReminderData();
        $this->assertTrue(($validator = new BodyIsRequired($data))->validate()->passes());
        $this->assertEmpty($validator->getErrors());
    }

    /** @test */
    public function fails_when_body_is_empty()
    {
        $data = $this->makeReminderData();
        $data->body = "";
        $validator = new BodyIsRequired($data);
        $this->assertTrue($validator->validate()->fails());
    }
}
