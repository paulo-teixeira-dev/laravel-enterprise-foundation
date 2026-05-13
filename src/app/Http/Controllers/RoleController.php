<?php

namespace App\Http\Controllers;

use App\Http\Requests\Authorization\PermissionRequest;
use App\Http\Requests\Role\RoleRequest;
use App\Http\Responses\ApiResponse;
use App\Services\RoleService;
//
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class RoleController extends Controller implements HasMiddleware
{
    public function __construct(private RoleService $service, private ApiResponse $apiResponse) {}

    public static function middleware(): array
    {
        return [
            'auth:api',
            new Middleware('permission:role.index', only: ['index']),
            new Middleware('permission:role.lookup', only: ['lookup']),
            new Middleware('permission:role.store', only: ['store']),
            new Middleware('permission:role.show', only: ['show']),
            new Middleware('permission:role.update', only: ['update']),
            new Middleware('permission:role.destroy', only: ['destroy']),
            new Middleware('permission:role.assign_permission', only: ['assignPermission']),
        ];
    }

    public function index(Request $request)
    {
        $data = $this->service->paginate($request->query());

        return $this->apiResponse->data($data)->json();
    }

    public function lookup(Request $request)
    {
        $data = $this->service->lookup($request->query());

        return $this->apiResponse->data($data)->json();
    }

    public function store(RoleRequest $request)
    {
        $this->service->create($request->validated());

        return $this->apiResponse->json();
    }

    public function show(int $id)
    {
        $data = $this->service->getById($id);

        return $this->apiResponse->data($data)->json();
    }

    public function update(int $id, RoleRequest $request)
    {
        $this->service->update($id, $request->validated());

        return $this->apiResponse->json();
    }

    public function destroy(int $id)
    {
        $this->service->delete($id);

        return $this->apiResponse->json();
    }

    public function assignPermission(int $id, PermissionRequest $request)
    {
        $this->service->assignPermission($id, $request->validated());

        return $this->apiResponse->json();
    }
}
