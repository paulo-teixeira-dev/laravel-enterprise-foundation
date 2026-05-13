<?php

namespace App\Services;

use App\Exceptions\BusinessException;
use App\Models\PersonProfile;
use App\Repositories\PersonRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Throwable;

class PersonService
{
    public function __construct(protected PersonRepository $repository) {}

    public function paginate(array $filters): LengthAwarePaginator
    {
        try {
            return $this->repository->paginate($filters);
        } catch (Throwable $e) {
            throw new BusinessException('', 500);
        }
    }

    public function lookup(array $filters): Collection
    {
        try {
            return $this->repository->lookup($filters);
        } catch (Throwable $e) {
            throw new BusinessException('', 500);
        }
    }

    public function create(array $data): void
    {
        DB::beginTransaction();
        try {
            $personProfile = $this->repository->create($data);
            $this->repository->createPersonContact($personProfile, $data['person_contact']);
            DB::commit();
        } catch (Throwable $e) {
            DB::rollBack();
            throw new BusinessException('', 500);
        }
    }

    public function getById(int $id): PersonProfile
    {
        try {
            return $this->repository->getById($id);
        } catch (ModelNotFoundException $e) {
            throw new BusinessException('', 404);
        } catch (Throwable $e) {
            throw new BusinessException('', 500);
        }
    }

    public function update(int $id, array $data): void
    {
        DB::beginTransaction();
        try {
            $person = $this->repository->getById($id);
            $this->repository->update($person, $data);
            if (isset($data['person_contact'])) {
                $this->repository->syncPersonContact($person, $data['person_contact']);
            }
            DB::commit();
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            throw new BusinessException('', 404);
        } catch (Throwable $e) {
            DB::rollBack();
            throw new BusinessException('', 500);
        }
    }

    public function delete(int $id): void
    {
        DB::beginTransaction();
        try {
            $person = $this->repository->getById($id);
            $this->repository->delete($person);
            DB::commit();
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            throw new BusinessException('', 404);
        } catch (Throwable $e) {
            DB::rollBack();
            throw new BusinessException('', 500);
        }
    }
}
