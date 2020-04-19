<?php

namespace Tests\Domain\Reminders\Rules;

use App\Domain\Reminders\Rules\CreateReminderValidation;
use Tests\TestCase;

class CreateReminderValidationTest extends TestCase
{
    /** @test */
    public function body_is_required()
    {
        // Should fail
        $data = $this->makeReminderData();
        $data->body = "";
        $validator = new CreateReminderValidation($data);
        $this->assertTrue($validator->validate()->fails());
        $this->assertArrayHasKey('body', $validator->getErrors());

        // Should pass
        $data->body = "Testing validation";
        $validator = new CreateReminderValidation($data);
        $this->assertTrue($validator->validate()->passes());
        $this->assertEmpty($validator->getErrors());
    }

    /** @test */
    public function date_must_be_valid()
    {
        // Should fail
        $data = $this->makeReminderData();
        $data->date = 0;
        $validator = new CreateReminderValidation($data);
        $this->assertTrue($validator->validate()->fails());
        $this->assertArrayHasKey('date', $validator->getErrors());

        // Should fail
        $data->date = 32;
        $validator = new CreateReminderValidation($data);
        $this->assertTrue($validator->validate()->fails());
        $this->assertArrayHasKey('date', $validator->getErrors());

        // Should pass
        foreach (range(1, 31) as $date) {
            $data->date = $date;
            $validator = new CreateReminderValidation($data);
            $this->assertTrue($validator->validate()->passes());
            $this->assertEmpty($validator->getErrors());
        }
    }

    /** @test */
    public function month_must_be_valid()
    {
        // Should fail
        $data = $this->makeReminderData();
        $data->month = -1;
        $validator = new CreateReminderValidation($data);
        $this->assertTrue($validator->validate()->fails());
        $this->assertArrayHasKey('month', $validator->getErrors());

        // Should fail
        $data->month = 12;
        $validator = new CreateReminderValidation($data);
        $this->assertTrue($validator->validate()->fails());
        $this->assertArrayHasKey('month', $validator->getErrors());

        // Should pass
        foreach (range(0, 11) as $month) {
            $data->month = $month;
            $validator = new CreateReminderValidation($data);
            $this->assertTrue($validator->validate()->passes());
            $this->assertEmpty($validator->getErrors());
        }
    }

    /** @test */
    public function time_must_be_valid()
    {
        // Should fail
        $data = $this->makeReminderData();
        $data->time = "";
        $validator = new CreateReminderValidation($data);
        $this->assertTrue($validator->validate()->fails());
        $this->assertArrayHasKey('time', $validator->getErrors());

        // Should fail
        $data->time = "35:98";
        $validator = new CreateReminderValidation($data);
        $this->assertTrue($validator->validate()->fails());
        $this->assertArrayHasKey('time', $validator->getErrors());

        // Should pass
        $data->time = "22:35";
        $validator = new CreateReminderValidation($data);
        $this->assertTrue($validator->validate()->passes());
        $this->assertEmpty($validator->getErrors());
    }

    /** @test */
    public function test_get_error()
    {
        $data = $this->makeReminderData();
        $data->body = "";
        $validator = (new CreateReminderValidation($data))->validate();
        $this->assertEquals("You must provide a body", $validator->getError('body'));

        // Should return false when trying to access a non-existing error
        $this->assertFalse($validator->getError('random-field'));
    }
}
