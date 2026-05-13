<?php

namespace App\Repositories;

use App\Models\File;
use Illuminate\Pagination\LengthAwarePaginator;

class FileRepository
{
    public function paginate(array $filters): LengthAwarePaginator
    {
        return File::paginate(10)->latest();
    }

    public function create(array $data): File
    {
        return File::create($data);
    }

    public function getById(int $id): File
    {
        return File::findOrFail($id);
    }

    public function delete(File $file): bool
    {
        return $file->delete();
    }
}
