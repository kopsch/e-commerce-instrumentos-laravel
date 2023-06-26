<?php

namespace App\Modules\Account\Auth\Requests;

use App\Modules\Base\BaseRequest;

class LogInRequest extends BaseRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'email'    => 'required|string|email|max:255',
            'password' => 'required|string|min:6|max:30',
        ];
    }

    public function attributeNames()
    {
        return [
            'email'    => 'E-mail',
            'password' => 'Senha'
        ];
    }

    public function messages()
    {
        return [
            //
        ];
    }
}
