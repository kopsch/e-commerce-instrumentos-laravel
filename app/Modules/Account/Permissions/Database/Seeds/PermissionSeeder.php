<?php

namespace App\Modules\Account\Permissions\Database\Seeds;

use App\Modules\Account\Permissions\Database\Seeds\Modules\UserPermissionSeeder;
use App\Modules\Account\Permissions\Database\Seeds\Modules\ProfilePermissionSeeder;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
			UserPermissionSeeder::class,
            ProfilePermissionSeeder::class,
        ]);
    }
}
