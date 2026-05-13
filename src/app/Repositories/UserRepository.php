<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
//
use Illuminate\Support\Collection;

class UserRepository
{
    public function paginate(array $filters): LengthAwarePaginator
    {
        return User::select([
            'id',
            'email',
            'active',
            'person_profile_id',
        ])
            ->with('personProfile:id,first_name', 'roles:id,name')
            ->latest()
            ->paginate($filters['per_page'] ?? 10);
    }

    public function lookup(array $filters): Collection
    {
        return User::select('id', 'email')
            ->when(
                $filters['search'] ?? null,
                fn ($q, $value) => $q->where('email', 'ilike', "%{$value}%")
            )->limit($filters['limit'] ?? 5)
            ->orderBy('email', 'asc')
            ->get();
    }

    public function create(array $data): User
    {
        return User::create($data);
    }

    public function getById(int $id): User
    {
        return User::select([
            'id',
            'email',
            'active',
            'updated_at',
            'person_profile_id',
        ])->with(['personProfile:id,first_name', 'roles:id,name'])->findOrFail($id);
    }

    public function getByEmail(string $email): User
    {
        return User::select([
            'id',
            'email',
            'active',
            'person_profile_id',
        ])
            ->with('personProfile')
            ->where('email', $email)
            ->firstOrFail();
    }

    public function update(User $user, array $data): bool
    {
        return $user->update($data);
    }

    public function syncRole(User $user, array $roles): User
    {
        return $user->syncRoles($roles);
    }
}
