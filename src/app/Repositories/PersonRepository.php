<?php

namespace App\Repositories;

use App\Models\PersonProfile;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class PersonRepository
{
    public function paginate(array $filters): LengthAwarePaginator
    {
        return PersonProfile::latest()->paginate($filters['per_page'] ?? 10);
    }

    public function lookup(array $filters): Collection
    {
        return PersonProfile::select('id', 'first_name')
            ->when(
                $filters['search'] ?? null,
                fn ($q, $value) => $q->where('first_name', 'ilike', "%{$value}%")
            )->limit($filters['limit'] ?? 5)
            ->orderBy('first_name', 'asc')
            ->get();
    }

    public function create(array $data): PersonProfile
    {
        return PersonProfile::create($data);
    }

    public function createPersonContact(PersonProfile $personProfile, array $data): PersonProfile
    {
        $personProfile->personContact()->createMany($data);

        return $personProfile->load('personContact');
    }

    public function getById(int $id): PersonProfile
    {
        return PersonProfile::with('personContact', 'state')->findOrFail($id);
    }

    public function update(PersonProfile $personProfile, array $data): bool
    {
        return $personProfile->update($data);
    }

    public function syncPersonContact(PersonProfile $personProfile, array $data): PersonProfile
    {
        $personProfile->personContact()->delete();
        $personProfile->personContact()->createMany($data);

        return $personProfile->load('personContact');
    }

    public function delete(PersonProfile $personProfile): bool
    {
        $personProfile->personContact()->delete();

        return $personProfile->delete();
    }
}
