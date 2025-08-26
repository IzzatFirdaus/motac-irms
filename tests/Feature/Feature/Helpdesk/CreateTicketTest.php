<?php

namespace Tests\Feature\Feature\Helpdesk;

use Tests\TestCase;

class CreateTicketTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_example(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }
}
