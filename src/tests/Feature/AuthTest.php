<?php

namespace Tests\Feature;

use App\Exceptions\BusinessException;
use App\Services\AuthService;
use Tests\TestCase;

class AuthTest extends TestCase
{
    public function test_login_returns_authenticated_user_data(): void
    {
        $payload = [
            'email' => 'admin@example.com',
            'password' => 'password',
        ];

        $this->mock(AuthService::class)
            ->shouldReceive('login')
            ->once()
            ->with($payload)
            ->andReturn([
                'first_name' => 'Admin',
                'last_name' => 'User',
                'email' => 'admin@example.com',
                'permissions' => ['user.index'],
                'token' => 'access-token',
            ]);

        $this->postJson('/api/v1/auth/login', $payload)
            ->assertOk()
            ->assertJsonPath('status', 200)
            ->assertJsonPath('data.first_name', 'Admin')
            ->assertJsonPath('data.last_name', 'User')
            ->assertJsonPath('data.email', 'admin@example.com')
            ->assertJsonPath('data.token', 'access-token')
            ->assertJsonPath('data.permissions', ['user.index']);
    }

    public function test_login_requires_email_and_password(): void
    {
        $this->mock(AuthService::class)
            ->shouldNotReceive('login');

        $this->postJson('/api/v1/auth/login', [])
            ->assertStatus(422)
            ->assertJsonPath('status', 422);
    }

    public function test_login_returns_unauthorized_for_invalid_credentials(): void
    {
        $payload = [
            'email' => 'admin@example.com',
            'password' => 'password',
        ];

        $this->mock(AuthService::class)
            ->shouldReceive('login')
            ->once()
            ->with($payload)
            ->andThrow(new BusinessException('Credenciais invalidas.', 401));

        $this->postJson('/api/v1/auth/login', $payload)
            ->assertUnauthorized()
            ->assertJsonPath('status', 401);
    }

    public function test_revoke_calls_auth_service(): void
    {
        $this->mock(AuthService::class)
            ->shouldReceive('revoke')
            ->once();

        $this->postJson('/api/v1/auth/revoke')
            ->assertOk()
            ->assertJsonPath('status', 200);
    }

    public function test_revoke_all_calls_auth_service(): void
    {
        $this->mock(AuthService::class)
            ->shouldReceive('revokeAll')
            ->once();

        $this->postJson('/api/v1/auth/revoke-all')
            ->assertOk()
            ->assertJsonPath('status', 200);
    }
}
