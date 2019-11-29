<?php

namespace Tests\Unit\Mail;

use App\Services\Notifications\Broadcaster;
use Tests\TestCase;

class BroadcasterTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }

    protected function getBroadcaster()
    {
        return new Broadcaster($this->app->getContainer());
    }

    /** @test */
    public function can_add_channels()
    {
        $broadcaster = $this->getBroadcaster();
        $initialChannelCount = count($broadcaster->getChannels());

        $broadcaster->mergeChannels($channels = [
            'test' => 'Tests\Channel\Test'
        ]);

        $this->assertArrayHasKey('test', $broadcaster->getChannels());
        $this->assertCount($initialChannelCount + count($channels), $broadcaster->getChannels());
    }

    /** @test */
    public function can_overwrite_all_channels()
    {
        $broadcaster = $this->getBroadcaster();
        $broadcaster->replaceChannels([
            'test' => 'Tests\Channel\Test',
        ]);
        $this->assertCount(1, $broadcaster->getChannels());
        $this->assertArrayHasKey('test', $broadcaster->getChannels());
    }

    /** @test */
    public function can_overwrite_a_single_channel()
    {
        $broadcaster = $this->getBroadcaster();
        $broadcaster->mergeChannels([
            'mail' => 'Custom::class'
        ]);
        $this->assertEquals('Custom::class', $broadcaster->getChannels()['mail']);
    }

    /** @test */
    public function can_retrieve_configured_channels()
    {
        $this->assertIsArray(($this->getBroadcaster())->getChannels());
    }

    /** @test */
    public function can_retrieve_default_channels()
    {
        $broadcaster = $this->getBroadcaster();
        $this->assertArrayHasKey('mail', $broadcaster->getDefaultChannels());
        $this->assertArrayHasKey('telegram', $broadcaster->getDefaultChannels());
    }

    /** @test */
    public function can_restore_default_channels()
    {
        $broadcaster = $this->getBroadcaster();

        $broadcaster->replaceChannels([
            'god' => 'TheOnlyChannel'
        ]);

        $this->assertArrayNotHasKey('mail', $broadcaster->getChannels());

        $broadcaster->restoreDefaultChannels();

        $this->assertArrayHasKey('mail', $broadcaster->getChannels());
        $this->assertCount(
            count($broadcaster->getDefaultChannels()),
            $broadcaster->getChannels()
        );
    }
}
