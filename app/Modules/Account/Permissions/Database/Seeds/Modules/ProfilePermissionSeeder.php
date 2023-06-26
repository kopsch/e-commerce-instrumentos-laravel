<?php

namespace App\Modules\Account\Permissions\Database\Seeds\Modules;

use App\Modules\Account\Permissions\Permission;
use App\Modules\Account\Permissions\PermissionCategory;
use Illuminate\Database\Seeder;

class ProfilePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $category = PermissionCategory::create([
            'name' => 'Perfil de Acesso',
            'type' => 'profiles'
        ]);

        $arrPermissions = [
            'Visualizar' => 'view',
            'Cadastrar' => 'store',
            'Alterar' => 'update',
            'Excluir' => 'delete',
            'Restaurar' => 'restore'
        ];

        foreach ($arrPermissions as $key => $item) {
            Permission::create([
                'name' => $key,
                'type' => $item,
                'permission_category_id' => $category->id
            ]);
        }
    }
}
