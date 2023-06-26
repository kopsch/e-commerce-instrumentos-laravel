<?php

namespace App\Modules\Account\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Modules\Account\Auth\AuthService;
use App\Modules\Account\Auth\Requests\LogInRequest;

class AuthController extends Controller
{
    private AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService          = $authService;
    }

    public function authenticate(LoginRequest $request)
    {
        return response()->json([
            'data'    => $this->authService->authenticate($request),
            'message' => __('auth::toasts.authenticate')
        ]);
    }

    public function logout(Request $request)
    {
        $this->authService->logout($request);

        return response()->json([
            'message' => __('auth::toasts.logout')
        ]);
    }
}

