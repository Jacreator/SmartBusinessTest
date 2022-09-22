<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests\LoginRequest;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\RegistrationRequest;

class AuthController extends Controller
{
    /**
     * Register a new user
     *
     * @param \App\Http\Requests\RegistrationRequest $request 
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(RegistrationRequest $request)
    {
        $user = User::create($this->_userDataToStore($request->validated()));

        $token = $user->createToken(config('auth.token'))->plainTextToken;

        return response()->json(
            [
                'message' => 'User created successfully',
                'user' => $user,
                'token' => $token
            ],
            201
        );
    }

    /**
     * Login User 
     * 
     * @param \App\Http\Requests\LoginRequest $request 
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(LoginRequest $request)
    {
        // Check email
        $user = User::where('email', $request->email)->first();

        // Check password
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(
                [
                    'message' => 'Invalid credentials'
                ],
                401
            );
        }

        $token = $user->createToken(config('auth.token'))->plainTextToken;

        return response()->json(
            [
                'message' => 'User logged in successfully',
                'user' => $user,
                'token' => $token
            ],
            200
        );
    }

    /**
     * Logout user (Revoke the token)
     * 
     * @return array
     */
    public function logout()
    {
        auth()->user()->tokens()->delete();

        return response()->json(
            [
                'message' => 'User logged out successfully'
            ],
            200
        );
    }

    /**
     * Get the authenticated User
     * 
     * @param array $data 
     * 
     * @return array
     */
    private function _userDataToStore(array $data): array
    {
        return [
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password'])
        ];
    }
}
