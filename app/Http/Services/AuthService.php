<?php

namespace App\Http\Services;

use App\Http\Repositories\UserRepository;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function register(array $data): array
    {
        $user = $this->userRepository->create($data);

        $token = $user->createToken('api_token')->plainTextToken;

        return [
            'user' => $user,
            'token' => $token
        ];
    }


    public function login(array $data): array
    {
        $user = $this->userRepository->getByEmail($data['email']);

        if (!Hash::check($data['password'], $user->password)) {
            abort(422, 'Invalid credentials.');
        }

        if (!$user) {
            abort(422, 'Invalid credentials.');
        }

        $user->tokens()->delete();
        $token = $user->createToken('api_token')->plainTextToken;

        return [
            'user' => $user,
            'token' => $token
        ];
    }

    public function logout($user): void
    {
        $user->currentAccessToken()->delete();
    }

}
