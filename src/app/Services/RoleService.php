<?php

namespace App\Services;

use App\Exceptions\BusinessException;
use App\Models\Role;
use App\Repositories\PermissionRepository;
use App\Repositories\RoleRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Pagination\LengthAwarePaginator;
//
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Throwable;

class RoleService
{
    public function __construct(protected RoleRepository $repository, protected PermissionRepository $permissionRepository) {}

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
            $role = $this->repository->create($data);
            $permissions = $this->permissionRepository->getNameById($data['permissions']);
            $this->repository->syncPermission($role, $permissions);
            DB::commit();
        } catch (Throwable $e) {
            DB::rollBack();
            throw new BusinessException('', 500);
        }
    }

    public function getById(int $id): Role
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
            $role = $this->repository->getById($id);
            $this->repository->update($role, $data);
            if (! empty($data['permissions'])) {
                $this->repository->syncPermission($role, $data['permissions']);
            }
            DB::commit();
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            throw new BusinessException('', 404);
        } catch (Throwable $e) {
            DB::rollBack();
            throw new BusinessException($e->getMessage(), 500);
        }
    }

    public function delete(int $id): void
    {
        DB::beginTransaction();
        try {
            $role = $this->repository->getById($id);
            $this->repository->delete($role);
            DB::commit();
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            throw new BusinessException('', 404);
        } catch (Throwable $e) {
            DB::rollBack();
            throw new BusinessException('', 500);
        }
    }

    public function assignPermission(string $id, array $data)
    {
        DB::beginTransaction();
        try {
            $role = $this->repository->getById($id);
            $permissions = $this->permissionRepository->getNameById($data['permissions']);
            $this->repository->syncPermission($role, $permissions);
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
