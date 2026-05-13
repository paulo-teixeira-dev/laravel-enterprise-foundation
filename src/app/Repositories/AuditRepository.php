<?php

namespace App\Repositories;

use App\Models\Activity;
use Illuminate\Pagination\LengthAwarePaginator;

class AuditRepository
{
    public function paginate(array $filters): LengthAwarePaginator
    {
        return Activity::with('causer:id,email')
            ->when($filters['email'] ?? null, function ($q, $email) {
                $q->whereHas('causer', function ($q) use ($email) {
                    $q->where('email', 'ilike', "%{$email}%");
                });
            })
            ->latest()
            ->paginate($filters['per_page'] ?? 10);
    }
}
