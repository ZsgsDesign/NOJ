<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MainAccessTest extends TestCase
{
    /**
     * A basic NOJ home access test.
     *
     * @return void
     */
    public function testHomeAccess()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }
}
