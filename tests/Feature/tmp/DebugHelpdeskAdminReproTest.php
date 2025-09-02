<?php

namespace Tests\Feature\tmp;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DebugHelpdeskAdminReproTest extends TestCase
{
    use RefreshDatabase;

    public function test_reproduce_admin_route_and_dump_response(): void
    {
        $this->markTestSkipped('Temporary debug test disabled');
    }
}
