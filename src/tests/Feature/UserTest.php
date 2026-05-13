<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\UserService;
use Illuminate\Pagination\LengthAwarePaginator;
use Tests\TestCase;

class UserTest extends TestCase
{
    public function test_index_returns_paginated_users(): void
    {
        $this->withoutMiddleware();

        $paginator = new LengthAwarePaginator([
            new User(['email' => 'admin@example.com', 'active' => true]),
        ], 1, 15);

        $this->mock(UserService::class)
            ->shouldReceive('paginate')
            ->once()
            ->with(['search' => 'admin'])
            ->andReturn($paginator);

        $this->getJson('/api/v1/users?search=admin')
            ->assertOk()
            ->assertJsonPath('status', 200)
            ->assertJsonPath('paginate.total', 1);
    }

    public function test_lookup_returns_users(): void
    {
        $this->withoutMiddleware();

        $this->mock(UserService::class)
            ->shouldReceive('lookup')
            ->once()
            ->with(['search' => 'admin'])
            ->andReturn(collect([
                ['id' => 1, 'email' => 'admin@example.com'],
            ]));

        $this->getJson('/api/v1/users/lookup?search=admin')
            ->assertOk()
            ->assertJsonPath('data.0.email', 'admin@example.com');
    }

    public function test_show_returns_user(): void
    {
        $this->withoutMiddleware();

        $this->mock(UserService::class)
            ->shouldReceive('getById')
            ->once()
            ->with(1)
            ->andReturn(new User(['email' => 'admin@example.com', 'active' => true]));

        $this->getJson('/api/v1/users/1')
            ->assertOk()
            ->assertJsonPath('data.email', 'admin@example.com');
    }

    public function test_store_validates_required_payload(): void
    {
        $this->withoutMiddleware();

        $this->mock(UserService::class)
            ->shouldNotReceive('create');

        $this->postJson('/api/v1/users', [])
            ->assertStatus(422)
            ->assertJsonPath('status', 422);
    }

    public function test_update_calls_user_service(): void
    {
        $this->withoutMiddleware();

        $payload = [
            'active' => false,
        ];

        $this->mock(UserService::class)
            ->shouldReceive('update')
            ->once()
            ->with(1, $payload);

        $this->putJson('/api/v1/users/1', $payload)
            ->assertOk()
            ->assertJsonPath('status', 200);
    }

    public function test_assign_role_validates_required_payload(): void
    {
        $this->withoutMiddleware();

        $this->mock(UserService::class)
            ->shouldNotReceive('assignRole');

        $this->putJson('/api/v1/users/1/roles', [])
            ->assertStatus(422)
            ->assertJsonPath('status', 422);
    }
}
