<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Services\RoleService;
use Illuminate\Pagination\LengthAwarePaginator;
use Tests\TestCase;

class RoleTest extends TestCase
{
    public function test_index_returns_paginated_roles(): void
    {
        $this->withoutMiddleware();

        $paginator = new LengthAwarePaginator([
            new Role(['name' => 'admin', 'description' => 'Administrator']),
        ], 1, 15);

        $this->mock(RoleService::class)
            ->shouldReceive('paginate')
            ->once()
            ->with(['search' => 'admin'])
            ->andReturn($paginator);

        $this->getJson('/api/v1/roles?search=admin')
            ->assertOk()
            ->assertJsonPath('status', 200)
            ->assertJsonPath('paginate.total', 1);
    }

    public function test_lookup_returns_roles(): void
    {
        $this->withoutMiddleware();

        $this->mock(RoleService::class)
            ->shouldReceive('lookup')
            ->once()
            ->with(['search' => 'admin'])
            ->andReturn(collect([
                ['id' => 1, 'name' => 'admin'],
            ]));

        $this->getJson('/api/v1/roles/lookup?search=admin')
            ->assertOk()
            ->assertJsonPath('data.0.name', 'admin');
    }

    public function test_show_returns_role(): void
    {
        $this->withoutMiddleware();

        $this->mock(RoleService::class)
            ->shouldReceive('getById')
            ->once()
            ->with(1)
            ->andReturn(new Role(['name' => 'admin', 'description' => 'Administrator']));

        $this->getJson('/api/v1/roles/1')
            ->assertOk()
            ->assertJsonPath('data.name', 'admin');
    }

    public function test_store_validates_required_payload(): void
    {
        $this->withoutMiddleware();

        $this->mock(RoleService::class)
            ->shouldNotReceive('create');

        $this->postJson('/api/v1/roles', [])
            ->assertStatus(422)
            ->assertJsonPath('status', 422);
    }

    public function test_update_calls_role_service(): void
    {
        $this->withoutMiddleware();

        $payload = [
            'name' => 'manager',
            'description' => 'Manager',
        ];

        $this->mock(RoleService::class)
            ->shouldReceive('update')
            ->once()
            ->with(1, $payload);

        $this->putJson('/api/v1/roles/1', $payload)
            ->assertOk()
            ->assertJsonPath('status', 200);
    }

    public function test_destroy_calls_role_service(): void
    {
        $this->withoutMiddleware();

        $this->mock(RoleService::class)
            ->shouldReceive('delete')
            ->once()
            ->with(1);

        $this->deleteJson('/api/v1/roles/1')
            ->assertOk()
            ->assertJsonPath('status', 200);
    }

    public function test_assign_permission_validates_required_payload(): void
    {
        $this->withoutMiddleware();

        $this->mock(RoleService::class)
            ->shouldNotReceive('assignPermission');

        $this->putJson('/api/v1/roles/1/permissions', [])
            ->assertStatus(422)
            ->assertJsonPath('status', 422);
    }
}
