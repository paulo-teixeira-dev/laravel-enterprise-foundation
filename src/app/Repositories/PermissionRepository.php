<?php

namespace App\Repositories;

use App\Models\Permission;
//
use Illuminate\Support\Collection;

class PermissionRepository
{
    public function getNameById(array $ids): array
    {
        $roles = Permission::whereIn('id', $ids)
            ->where('guard_name', 'api')
            ->get();

        $names = $roles->pluck('name')->toArray();

        return $names;
    }

    public function lookup(array $filters): Collection
    {
        return Permission::select('id', 'name')
            ->when(
                $filters['search'] ?? null,
                fn ($q, $value) => $q->where('name', 'ilike', "%{$value}%")
            )->limit($filters['limit'] ?? 5)
            ->orderBy('name', 'asc')
            ->get();
    }
}
