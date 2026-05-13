<?php

namespace App\Http\Controllers;

use App\Http\Requests\Person\PersonRequest;
use App\Http\Responses\ApiResponse;
use App\Services\PersonService;
use Illuminate\Http\Request;
//
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class PersonController extends Controller implements HasMiddleware
{
    public function __construct(private PersonService $service, private ApiResponse $apiResponse) {}

    public static function middleware(): array
    {
        return [
            'auth:api',
            new Middleware('permission:person.index', only: ['index']),
            new Middleware('permission:person.lookup', only: ['lookup']),
            new Middleware('permission:person.store', only: ['store']),
            new Middleware('permission:person.show', only: ['show']),
            new Middleware('permission:person.update', only: ['update']),
            new Middleware('permission:person.destroy', only: ['destroy']),
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

    public function store(PersonRequest $request)
    {
        $this->service->create($request->validated());

        return $this->apiResponse->json();
    }

    public function show(int $id)
    {
        $data = $this->service->getById($id);

        return $this->apiResponse->data($data)->json();
    }

    public function update(int $id, PersonRequest $request)
    {
        $this->service->update($id, $request->validated());

        return $this->apiResponse->json();
    }

    public function destroy(int $id)
    {
        $this->service->delete($id);

        return $this->apiResponse->json();
    }
}
