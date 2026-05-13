<?php

namespace App\Services;

use App\Exceptions\BusinessException;
use App\Repositories\StateRepository;
use Illuminate\Support\Collection;
//
use Throwable;

class StateService
{
    public function __construct(protected StateRepository $repository) {}

    public function lookup(array $filters): Collection
    {
        try {
            return $this->repository->lookup($filters);
        } catch (Throwable $e) {
            throw new BusinessException('', 500);
        }
    }
}
