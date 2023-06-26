<?php

namespace App\Modules\Account\Exceptions;

use App\Modules\Account\Auth\Requests\LogInRequest;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * Exceção lançada quando o usuário insere credenciais inválidas no login
 */
class InvalidCredentialsException extends HttpException
{
    public function __construct(LogInRequest $request)
    {
        parent::__construct(422, __('auth::toasts.invalid_credentials'));
    }
}
