<?php

namespace App\Modules\Account;

use App\Modules\Account\Auth\AuthServiceProvider;
use App\Modules\Account\Permissions\PermissionServiceProvider;
use App\Modules\Account\Profiles\ProfileServiceProvider;
use App\Modules\Account\Users\UserServiceProvider;
use Illuminate\Support\ServiceProvider;

class AccountServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->register(ProfileServiceProvider::class);
        $this->app->register(UserServiceProvider::class);
        $this->app->register(AuthServiceProvider::class);
        $this->app->register(PermissionServiceProvider::class);
    }
}
