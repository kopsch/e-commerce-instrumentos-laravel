<?php

namespace App\Modules\Account\Permissions\Database\Seeds;

use App\Modules\Account\Permissions\Permission;
use App\Modules\Account\Permissions\PermissionCategory;
use Illuminate\Database\Seeder;

class AccessTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $access_type = PermissionCategory::create([
            'name' => 'Tipo de Acesso',
            'type' => 'access_type',
            'multiple' => 0
        ]);

        Permission::create([
            'name' => 'Administrativo',
            'type' => 'admin',
            'permission_category_id' => $access_type->id
        ]);

        Permission::create([
            'name' => 'Regional',
            'type' => 'regional',
            'permission_category_id' => $access_type->id
        ]);

        Permission::create([
            'name' => 'Gerencial',
            'type' => 'manager',
            'permission_category_id' => $access_type->id
        ]);

        Permission::create([
            'name' => 'Corretor',
            'type' => 'realtor',
            'permission_category_id' => $access_type->id
        ]);
    }
}
