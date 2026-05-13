<?php

namespace App\Http\Controllers;

use App\Http\Responses\ApiResponse;
use App\Services\PermissionService;
use Illuminate\Http\Request;
//
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class PermissionController extends Controller implements HasMiddleware
{
    public function __construct(private PermissionService $service, private ApiResponse $apiResponse) {}

    public static function middleware(): array
    {
        return [
            'auth:api',
            new Middleware('permission:permission.lookup', only: ['lookup']),
        ];
    }

    public function lookup(Request $request)
    {
        $data = $this->service->lookup($request->query());

        return $this->apiResponse->data($data)->json();
    }
}
