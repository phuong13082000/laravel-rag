<?php

namespace Modules\Auth\Services;

use Modules\User\Models\User;
use Modules\Auth\Dtos\RegisterDTO;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    public function register(RegisterDTO $dto): array
    {
        $user = User::create([
            'name' => $dto->name,
            'email' => $dto->email,
            'password' => Hash::make($dto->password),
        ]);

        $token = $user->createToken('api')->plainTextToken;

        return [
            'user' => $user,
            'token' => $token,
        ];
    }

    public function login(array $data): ?array
    {
        $user = User::where('email', $data['email'])->first();

        if (!$user || !Hash::check($data['password'], $user->password)) {
            return null;
        }

        return [
            'user' => $user,
        ];
    }

    public function logout(User $user): void
    {
        $user->tokens()->delete();
    }
}
