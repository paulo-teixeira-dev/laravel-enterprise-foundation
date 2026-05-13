<?php

namespace Tests\Feature;

use App\Services\AuditService;
use Illuminate\Pagination\LengthAwarePaginator;
use Tests\TestCase;

class AuditTest extends TestCase
{
    public function test_index_returns_paginated_audits(): void
    {
        $this->withoutMiddleware();

        $paginator = new LengthAwarePaginator([
            ['id' => 1, 'description' => 'created'],
        ], 1, 15);

        $this->mock(AuditService::class)
            ->shouldReceive('paginate')
            ->once()
            ->with(['event' => 'created'])
            ->andReturn($paginator);

        $this->getJson('/api/v1/audit?event=created')
            ->assertOk()
            ->assertJsonPath('status', 200)
            ->assertJsonPath('paginate.total', 1);
    }
}
