<?php

namespace Tests\Domain\Recurrence;

use App\Domain\Recurrences\RecurrencesSupport;
use Tests\TestCase;

class RecurrencesSupportTest extends TestCase
{
    /** @test */
    public function is_recurring_returns_the_expected_results()
    {
        // Test some non-existent frequencies
        $this->assertFalse(RecurrencesSupport::isFrequencyValid('none'));
        $this->assertFalse(RecurrencesSupport::isFrequencyValid('non-existent-recurrence'));

        // Loop over frequencies from the Domain and assert they are valid
        foreach (array_keys(RecurrencesSupport::frequencies()) as $frequency) {
            $this->assertTrue(RecurrencesSupport::isFrequencyValid($frequency));
        }
    }
}
