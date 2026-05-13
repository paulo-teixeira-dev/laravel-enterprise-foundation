<?php

namespace Tests\Feature;

use App\Services\PermissionService;
use App\Services\StateService;
use Tests\TestCase;

class LookupTest extends TestCase
{
    public function test_permissions_lookup_returns_permissions(): void
    {
        $this->withoutMiddleware();

        $this->mock(PermissionService::class)
            ->shouldReceive('lookup')
            ->once()
            ->with(['search' => 'user'])
            ->andReturn(collect([
                ['id' => 1, 'name' => 'user.index'],
            ]));

        $this->getJson('/api/v1/permissions/lookup?search=user')
            ->assertOk()
            ->assertJsonPath('data.0.name', 'user.index');
    }

    public function test_states_lookup_returns_states(): void
    {
        $this->withoutMiddleware();

        $this->mock(StateService::class)
            ->shouldReceive('lookup')
            ->once()
            ->with(['search' => 'sp'])
            ->andReturn(collect([
                ['id' => 1, 'uf' => 'SP'],
            ]));

        $this->getJson('/api/v1/states/lookup?search=sp')
            ->assertOk()
            ->assertJsonPath('data.0.uf', 'SP');
    }
}
