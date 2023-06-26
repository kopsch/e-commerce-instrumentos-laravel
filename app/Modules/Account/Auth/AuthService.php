<?php

namespace App\Modules\Account\Auth;

use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Modules\Account\Users\User;
use Illuminate\Support\Facades\Auth;
use App\Modules\Account\Users\UserService;
use App\Modules\Account\Auth\Requests\LogInRequest;
use App\Modules\Account\Exceptions\InvalidCredentialsException;
use Illuminate\Database\Eloquent\Builder;


class AuthService
{
    private User $user;
    private string $token;
    private Builder $localQuery;
    private LogInRequest $request;

    /**
     * Realiza a autenticação e retorna usuário e token.
     */
    public function authenticate(LoginRequest $request)
    {
        $this->setRequest($request);
        $this->setLocalQuery();

        if ($this->userExistsLocally()) {
            $this->authenticateLocally();
        } else {
            throw new InvalidCredentialsException($request);
        }


        return [
            'token' => $this->getToken(),
            'user'  => $this->getAuthenticatedUser()
        ];
    }

    private function setRequest(LogInRequest $request)
    {
        $this->request = $request;
    }

    private function setLocalQuery()
    {
        $this->localQuery = User::where('email', $this->request->email);
    }

    private function login()
    {
        $this->setUser();
        $this->setToken();
        $this->generateSession();
    }

    private function userExistsLocally()
    {
        return $this->localQuery->exists();
    }

    private function authenticateLocally()
    {
        if (Auth::attempt(['email' => $this->request->email, 'password' => $this->request->password])) {
            return $this->login();
        }

        throw new InvalidCredentialsException($this->request);
    }

    private function setUser()
    {
        $user = $this->localQuery->first();

        $this->user = $user;
    }

    private function setToken()
    {
        $this->token = JWTAuth::fromUser(
            $this->user
        );
    }

    private function getToken()
    {
        return $this->token;
    }

    private function generateSession()
    {
        auth('api')->login(
            $this->user
        );
    }

    private function getAuthenticatedUser()
    {
        return app()->make(UserService::class)->getAuthenticatedUser($this->user);
    }

    public function logout(Request $request)
    {
        JWTAuth::invalidate(JWTAuth::getToken());
    }
}
