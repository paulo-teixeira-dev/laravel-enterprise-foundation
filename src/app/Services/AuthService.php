<?php

namespace App\Services;

use App\Exceptions\BusinessException;
use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Contracts\Auth\Factory as AuthFactory;
use Laravel\Passport\Token;
use Throwable;

class AuthService
{
    public function __construct(protected AuthFactory $auth, protected UserRepository $userRepository) {}

    public function login(array $credentials): array
    {
        try {
            if (! $this->auth->guard('web')->validate($credentials)) {
                throw new BusinessException('Credenciais inválidas.', 401);
            }

            $user = $this->userRepository->getByEmail($credentials['email']);

            if (! $user->active) {
                throw new BusinessException('Credenciais inválidas', 401);
            }

            $user->load(['personProfile', 'permissions', 'roles.permissions']);
            $permissions = $user->getAllPermissions()->pluck('name')->toArray();

            $data = [
                'first_name' => $user->personProfile?->first_name,
                'last_name' => $user->personProfile?->last_name,
                'email' => $user->email,
                'permissions' => $permissions,
                'token' => $user->createToken('login')->accessToken,
            ];

            return $data;
        } catch (BusinessException $e) {
            throw $e;
        } catch (Throwable $e) {
            throw new BusinessException($e->getMessage(), 500);
        }
    }

    public function revoke(): void
    {
        try {
            /** @var User $user */
            $user = $this->auth->guard()->user();

            if (! $user) {
                throw new BusinessException('', 404);
            }

            /** @var Token $token */
            $token = $user->token();
            $token->revoke();
        } catch (BusinessException $e) {
            throw $e;
        } catch (Throwable $e) {
            throw new BusinessException('', 500);
        }
    }

    public function revokeAll(): void
    {
        try {
            /** @var User $user */
            $user = $this->auth->guard()->user();

            if (! $user) {
                throw new BusinessException('', 404);
            }

            $user->tokens()->update(['revoked' => true]);
        } catch (BusinessException $e) {
            throw $e;
        } catch (Throwable $e) {
            throw new BusinessException('', 500);
        }
    }
}
