<?php

namespace App\Http\Controllers;

use App\Http\Requests\File\FileRequest;
use App\Http\Responses\ApiResponse;
use App\Services\FileService;
use Illuminate\Http\Request;
//
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class FileController extends Controller implements HasMiddleware
{
    public function __construct(private FileService $service, private ApiResponse $apiResponse) {}

    public static function middleware(): array
    {
        return [
            'auth:api',
            new Middleware('permission:file.index', only: ['index']),
            new Middleware('permission:file.store', only: ['store']),
            new Middleware('permission:file.show', only: ['show']),
            new Middleware('permission:file.download', only: ['download']),
            new Middleware('permission:file.destroy', only: ['destroy']),
        ];
    }

    public function index(Request $request)
    {
        $data = $this->service->paginate($request->query());

        return $this->apiResponse->data($data)->json();
    }

    public function store(FileRequest $request)
    {
        $this->service->create($request->file('file'));

        return $this->apiResponse->json();
    }

    public function show(int $id)
    {
        $data = $this->service->getById($id);

        return $this->apiResponse->stream($data['content'], $data['headers']);
    }

    public function download(int $id)
    {
        $data = $this->service->download($id);

        return $data;
    }

    public function destroy(int $id)
    {
        $this->service->delete($id);

        return $this->apiResponse->json();
    }
}
