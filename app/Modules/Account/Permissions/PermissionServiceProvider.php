<?php

namespace App\Modules\Account\Permissions;

use App\Modules\Base\BaseServiceProvider;

class PermissionServiceProvider extends BaseServiceProvider
{
    protected string $directory = __DIR__;

    protected string $namespace = __NAMESPACE__;

    protected string $name = 'permissions';

    public function boot()
    {
        parent::boot();
        $this->mapRoutes();
    }
}
