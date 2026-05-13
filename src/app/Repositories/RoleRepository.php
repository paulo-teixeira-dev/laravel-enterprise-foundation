<?php

namespace App\Repositories;

use App\Models\Role;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class RoleRepository
{
    public function paginate(array $filters): LengthAwarePaginator
    {
        return Role::select('id', 'name')->latest()->paginate($filters['per_page'] ?? 10);
    }

    public function lookup(array $filters): Collection
    {
        return Role::select('id', 'name')->when(
            $filters['search'] ?? null,
            fn ($q, $value) => $q->where('name', 'ilike', "%{$value}%")
        )->limit($filters['limit'] ?? 5)
            ->orderBy('name', 'asc')
            ->get();
    }

    public function create(array $data): Role
    {
        return Role::create($data);
    }

    public function getById(int $id): Role
    {
        return Role::with('permissions')->findOrFail($id);
    }

    public function update(Role $role, array $data): bool
    {
        return $role->update($data);
    }

    public function delete(Role $role): bool
    {
        return $role->delete();
    }

    public function getNameById(array $ids): array
    {
        $roles = Role::whereIn('id', $ids)
            ->where('guard_name', 'api')
            ->get();

        $names = $roles->pluck('name')->toArray();

        return $names;
    }

    public function syncPermission(Role $role, array $permissions): Role
    {
        return $role->syncPermissions($permissions);
    }
}
