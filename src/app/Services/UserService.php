<?php

namespace App\Services;

use App\Exceptions\BusinessException;
use App\Models\User;
use App\Repositories\RoleRepository;
use App\Repositories\UserRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Pagination\LengthAwarePaginator;
//
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Throwable;

class UserService
{
    public function __construct(protected UserRepository $repository, protected RoleRepository $roleRepository) {}

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
            $user = $this->repository->create($data);
            $roles = $this->roleRepository->getNameById($data['roles']);
            $this->repository->syncRole($user, $roles);
            DB::commit();
        } catch (Throwable $e) {
            DB::rollBack();
            throw new BusinessException($e->getMessage(), 500);
        }
    }

    public function getById(int $id): ?User
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
            $user = $this->repository->getById($id);
            $this->repository->update($user, $data);
            if (! empty($data['roles'])) {
                $this->repository->syncRole($user, $data['roles']);
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

    public function assignRole(string $id, array $data)
    {
        DB::beginTransaction();
        try {
            $user = $this->repository->getById($id);
            $roles = $this->roleRepository->getNameById($data['roles']);
            $this->repository->syncRole($user, $roles);
            DB::commit();
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            throw new BusinessException('', 404);
        } catch (Throwable $e) {
            DB::rollBack();
            throw new BusinessException($e->getMessage(), 500);
        }
    }
}
