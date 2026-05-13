<?php

namespace App\Http\Controllers;

use App\Http\Responses\ApiResponse;
use App\Services\AuditService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
//
use Illuminate\Routing\Controllers\Middleware;

class AuditController extends Controller implements HasMiddleware
{
    public function __construct(private AuditService $service, private ApiResponse $apiResponse) {}

    public static function middleware(): array
    {
        return [
            'auth:api',
            new Middleware('permission:audit.index', only: ['index']),
        ];
    }

    public function index(Request $request)
    {
        $data = $this->service->paginate($request->query());

        return $this->apiResponse->data($data)->json();
    }
}
