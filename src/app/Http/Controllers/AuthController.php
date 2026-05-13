<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\LoginRequest;
use App\Http\Responses\ApiResponse;
//
use App\Services\AuthService;

class AuthController extends Controller
{
    public function __construct(private AuthService $service, private ApiResponse $apiResponse) {}

    public function login(LoginRequest $request)
    {
        $data = $this->service->login($request->validated());

        return $this->apiResponse->data($data)->json();
    }

    public function revoke()
    {
        $this->service->revoke();

        return $this->apiResponse->json();
    }

    public function revokeAll()
    {
        $this->service->revokeAll();

        return $this->apiResponse->json();
    }
}
