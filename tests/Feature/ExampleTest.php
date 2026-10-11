<?php

namespace Tests\Feature;

use Database\Seeders\WeddingSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * The homepage needs the weddings row the whole site is built on,
     * so run the real seeder against the fresh test database.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $this->seed(WeddingSeeder::class);

        $response = $this->get('/');

        $response->assertStatus(200);
    }
}
