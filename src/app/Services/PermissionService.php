<?php

namespace App\Services;

use App\Exceptions\BusinessException;
use App\Repositories\PermissionRepository;
use Illuminate\Support\Collection;
use Throwable;

class PermissionService
{
    public function __construct(protected PermissionRepository $repository) {}

    public function lookup(array $filters): Collection
    {
        try {
            return $this->repository->lookup($filters);
        } catch (Throwable $e) {
            throw new BusinessException('', 500);
        }
    }
}
