<?php

namespace App\Modules\Account\Auth;

use App\Modules\Base\BaseServiceProvider;

class AuthServiceProvider extends BaseServiceProvider
{
    protected string $directory = __DIR__;

    protected string $namespace = __NAMESPACE__;

    protected string $name = 'auth';

    public function boot()
    {
        parent::boot();
        $this->mapRoutes('api/auth', ['api']);
    }
}
