<?php

namespace App\Http\Controllers;

use App\Http\Requests\Authorization\RoleRequest;
use App\Http\Requests\User\UserRequest;
use App\Http\Responses\ApiResponse;
use App\Services\UserService;
//
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class UserController extends Controller implements HasMiddleware
{
    public function __construct(private UserService $service, private ApiResponse $apiResponse) {}

    public static function middleware(): array
    {
        return [
            'auth:api',
            new Middleware('permission:user.index', only: ['index']),
            new Middleware('permission:user.lookup', only: ['lookup']),
            new Middleware('permission:user.store', only: ['store']),
            new Middleware('permission:user.show', only: ['show']),
            new Middleware('permission:user.update', only: ['update']),
            new Middleware('permission:user.assign_role', only: ['assignRole']),
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

    public function store(UserRequest $request)
    {
        $this->service->create($request->validated());

        return $this->apiResponse->json();
    }

    public function show(int $id)
    {
        $data = $this->service->getById($id);

        return $this->apiResponse->data($data)->json();
    }

    public function update(int $id, UserRequest $request)
    {
        $this->service->update($id, $request->validated());

        return $this->apiResponse->json();
    }

    public function assignRole(int $id, RoleRequest $request)
    {
        $this->service->assignRole($id, $request->validated());

        return $this->apiResponse->json();
    }
}
