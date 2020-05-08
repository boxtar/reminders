<?php

namespace Tests\Application;

use App\Response;
use Tests\TestCase;

class ResponseTest extends TestCase
{
    /** @test */
    public function can_get_and_set_the_status()
    {
        $response = Response::create();
        $response->setStatus(200);
        $this->assertEquals(200, $response->getStatus());
    }

    /** @test */
    public function can_get_and_set_response_data()
    {
        $response = Response::create();
        $response->setData([
            'message' => 'test response'
        ]);
        $this->assertIsArray($response->getData());
        $this->assertArrayHasKey('message', $response->getData());
        $this->assertEquals('test response', $response->getData()['message']);
    }

    /** @test */
    public function can_get_and_set_errors()
    {
        $response = Response::create();
        $response->setErrors([
            'body' => 'body is required',
            'date' => 'date is not valid'
        ]);
        $this->assertIsArray($response->getErrors());
        $this->assertArrayHasKey('body', $response->getErrors());
        $this->assertEquals('date is not valid', $response->getErrors()['date']);
    }
}
