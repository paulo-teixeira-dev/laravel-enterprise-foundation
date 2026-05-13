<?php

namespace App\Services;

use App\Exceptions\BusinessException;
use App\Repositories\FileRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Throwable;

class FileService
{
    protected array $directoryMap = [
        'image' => 'images',
        'application' => 'documents',
        'video' => 'videos',
        'audio' => 'audio',
        'text' => 'documents',
    ];

    public function __construct(protected FileRepository $repository) {}

    public function paginate(array $filters): LengthAwarePaginator
    {
        try {
            return $this->repository->paginate($filters);
        } catch (Throwable $e) {
            throw new BusinessException('', 500);
        }
    }

    public function create(UploadedFile $file, string $disk = 'local'): void
    {
        DB::beginTransaction();
        try {

            $path = $this->getDirectoryByMime($file->getMimeType());
            $name = $file->hashName();

            $this->repository->create([
                'original_name' => $file->getClientOriginalName(),
                'name' => $name,
                'path' => $path,
                'mime_type' => $file->getMimeType(),
                'size' => $file->getSize(),
                'disk' => $disk,
            ]);

            $file->storeAs($path, $name, $disk);

            DB::commit();
        } catch (Throwable $e) {
            DB::rollBack();
            throw new BusinessException($e->getMessage(), 500);
        }
    }

    public function getById(int $id): array
    {
        try {
            $file = $this->repository->getById($id);
            $fullPath = $file->path.'/'.$file->name;

            if (! Storage::disk($file->disk)->exists($fullPath)) {
                throw new BusinessException('file not found', 404);
            }

            $fileContent = Storage::disk($file->disk)->get($fullPath);
            $data = ['content' => $fileContent, 'headers' => ['Content-Type' => $file->mime_type, 'Content-Disposition' => 'inline; filename="'.$file->original_name.'"']];

            return $data;
        } catch (ModelNotFoundException $e) {
            throw new BusinessException('', 404);
        } catch (BusinessException $e) {
            throw $e;
        } catch (Throwable $e) {
            throw new BusinessException('', 500);
        }
    }

    public function download(int $id)
    {
        try {
            $file = $this->repository->getById($id);
            $fullPath = $file->path.'/'.$file->name;

            if (! Storage::disk($file->disk)->exists($fullPath)) {
                throw new BusinessException('file not found', 404);
            }

            $data = Storage::disk($file->disk)->download($fullPath, $file->original_name);

            return $data;
        } catch (ModelNotFoundException $e) {
            throw new BusinessException('', 404);
        } catch (BusinessException $e) {
            throw $e;
        } catch (Throwable $e) {
            throw new BusinessException('', 500);
        }
    }

    public function delete(int $id): void
    {
        DB::beginTransaction();
        try {
            $file = $this->repository->getById($id);
            $fullPath = $file->path.'/'.$file->name;

            $this->repository->delete($file);

            if (! Storage::disk($file->disk)->exists($fullPath)) {
                throw new BusinessException('file not found', 404);
            }

            Storage::disk($file->disk)->delete($fullPath);

            DB::commit();
        } catch (ModelNotFoundException $e) {
            throw new BusinessException('', 404);
        } catch (BusinessException $e) {
            throw $e;
        } catch (Throwable $e) {
            throw new BusinessException('', 500);
        }
    }

    private function getDirectoryByMime(string $mime): string
    {
        $type = explode('/', $mime)[0];

        return $this->directoryMap[$type] ?? 'others';
    }
}
