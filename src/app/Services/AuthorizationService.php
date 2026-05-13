<?php

namespace App\Services;

use App\Exceptions\BusinessException;
use App\Repositories\RolePermissionRepository;
use App\Repositories\UserRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Throwable;

class AuthorizationService
{
    public function __construct(protected RolePermissionRepository $repository, protected UserRepository $userRepository) {}

    public function assignPermissionToRole(string $id, array $data)
    {
        DB::beginTransaction();
        try {
            $role = $this->repository->getRoleById($id);
            $permissions = $this->repository->getPermissionsNameById($data['permissions']);
            $this->repository->syncPermissionsToRole($role, $permissions);
            DB::commit();
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            throw new BusinessException('', 404);
        } catch (Throwable $e) {
            DB::rollBack();
            throw new BusinessException($e->getMessage(), 500);
        }
    }

    public function assignRoleToUser(string $id, array $data)
    {
        DB::beginTransaction();
        try {
            $user = $this->userRepository->getById($id);
            $roles = $this->repository->getRolesNameById($data['roles']);
            $this->repository->syncRoleToUser($user, $roles);
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
