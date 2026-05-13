<?php

namespace App\Repositories;

use App\Models\State;
use Illuminate\Support\Collection;

class StateRepository
{
    public function lookup(array $filters): Collection
    {
        return State::select(['id', 'name'])
            ->when(
                $filters['search'] ?? null,
                fn ($q, $value) => $q->where('name', 'ilike', "%{$value}%")
            )->limit($filters['limit'] ?? 5)
            ->orderBy('name', 'asc')
            ->get();
    }
}
