<?php

namespace Tests\Feature;

use App\Models\PersonProfile;
use App\Services\PersonService;
use Illuminate\Pagination\LengthAwarePaginator;
use Tests\TestCase;

class PersonTest extends TestCase
{
    public function test_index_returns_paginated_persons(): void
    {
        $this->withoutMiddleware();

        $paginator = new LengthAwarePaginator([
            new PersonProfile(['first_name' => 'Maria', 'last_name' => 'Silva']),
        ], 1, 15);

        $this->mock(PersonService::class)
            ->shouldReceive('paginate')
            ->once()
            ->with(['search' => 'maria'])
            ->andReturn($paginator);

        $this->getJson('/api/v1/persons?search=maria')
            ->assertOk()
            ->assertJsonPath('status', 200)
            ->assertJsonPath('paginate.total', 1);
    }

    public function test_lookup_returns_persons(): void
    {
        $this->withoutMiddleware();

        $this->mock(PersonService::class)
            ->shouldReceive('lookup')
            ->once()
            ->with(['search' => 'maria'])
            ->andReturn(collect([
                ['id' => 1, 'name' => 'Maria Silva'],
            ]));

        $this->getJson('/api/v1/persons/lookup?search=maria')
            ->assertOk()
            ->assertJsonPath('data.0.name', 'Maria Silva');
    }

    public function test_show_returns_person(): void
    {
        $this->withoutMiddleware();

        $this->mock(PersonService::class)
            ->shouldReceive('getById')
            ->once()
            ->with(1)
            ->andReturn(new PersonProfile(['first_name' => 'Maria', 'last_name' => 'Silva']));

        $this->getJson('/api/v1/persons/1')
            ->assertOk()
            ->assertJsonPath('data.first_name', 'Maria');
    }

    public function test_store_validates_required_payload(): void
    {
        $this->withoutMiddleware();

        $this->mock(PersonService::class)
            ->shouldNotReceive('create');

        $this->postJson('/api/v1/persons', [])
            ->assertStatus(422)
            ->assertJsonPath('status', 422);
    }

    public function test_update_calls_person_service(): void
    {
        $this->withoutMiddleware();

        $payload = [
            'first_name' => 'Maria',
        ];

        $this->mock(PersonService::class)
            ->shouldReceive('update')
            ->once()
            ->with(1, $payload);

        $this->putJson('/api/v1/persons/1', $payload)
            ->assertOk()
            ->assertJsonPath('status', 200);
    }

    public function test_destroy_calls_person_service(): void
    {
        $this->withoutMiddleware();

        $this->mock(PersonService::class)
            ->shouldReceive('delete')
            ->once()
            ->with(1);

        $this->deleteJson('/api/v1/persons/1')
            ->assertOk()
            ->assertJsonPath('status', 200);
    }
}
