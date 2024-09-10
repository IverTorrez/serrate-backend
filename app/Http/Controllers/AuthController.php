<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\AuthService;

class AuthController extends Controller
{

    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function register(Request $request)
    {
        $result = $this->authService->register($request->all());

        if (isset($result['errors'])) {
            return response()->json($result['errors'], 400);
        }

        if (isset($result['error'])) {
            return response()->json(['error' => $result['error']], $result['status']);
        }

        return response()->json($result);
    }


    public function login(Request $request)
    {
        $result = $this->authService->login($request->only('email', 'password'));

        if (isset($result['status'])) {
            return response()->json(['message' => $result['message']], $result['status']);
        }

        return response()->json($result);
    }


    public function logout()
    {
        $result = $this->authService->logout();
        return response()->json($result);
    }
}
