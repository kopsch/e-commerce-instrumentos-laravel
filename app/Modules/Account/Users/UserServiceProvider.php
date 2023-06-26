<?php

namespace App\Modules\Account\Users;

use App\Modules\Base\BaseServiceProvider;

class UserServiceProvider extends BaseServiceProvider
{
    protected string $directory = __DIR__;

    protected string $namespace = __NAMESPACE__;

    protected string $name = 'users';

    protected array $morphMaps = [
        'users' => User::class
    ];

    protected array $policies = [
        User::class => UserPolicy::class
    ];

    public function boot()
    {
        parent::boot();
        $this->mapRoutes('api/users');
    }
}
