<?php

namespace App\Services;

use App\Exceptions\BusinessException;
use App\Repositories\AuditRepository;
use Illuminate\Pagination\LengthAwarePaginator;
//
use Throwable;

class AuditService
{
    public function __construct(protected AuditRepository $repository) {}

    public function paginate(array $filters): LengthAwarePaginator
    {
        try {
            return $this->repository->paginate($filters);
        } catch (Throwable $e) {
            throw new BusinessException('', 500);
        }
    }
}
