<?php

namespace Modules\Auth\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Modules\Auth\Dtos\RegisterDTO;
use Modules\Auth\Services\AuthService;

class AuthApiController extends Controller
{
    public function __construct(
        private readonly AuthService $authService
    ) {}

    public function register(Request $request)
    {
        $dto = new RegisterDTO(
            name: $request->input('name'),
            email: $request->input('email'),
            password: $request->input('password'),
        );

        $user = $this->authService->register($dto);

        return response()->json([
            'success' => true,
            'data' => $user,
            'message' => 'Registration successful',
        ]);
    }

    public function login(Request $request)
    {
        $data = $request->only(['email', 'password']);
        $user = $this->authService->login($data);

        if (!$user) {
            return response()->json([
                'success' => false,
                'error' => 'Invalid credentials'
            ], 401);
        }

        return response()->json([
            'success' => true,
            'data' => $user['user'],
            'message' => 'Login successful',
        ]);
    }

    public function logout(Request $request)
    {
        $this->authService->logout($request->user());

        return response()->json([
            'success' => true,
            'message' => 'Logout successful',
        ]);
    }

    public function me(Request $request)
    {
        return response()->json([
            'success' => true,
            'data' => $request->user(),
            'message' => 'User retrieved successfully',
        ]);
    }
}
