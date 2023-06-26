<?php

namespace App\Modules\Account\Permissions;

use App\Modules\Account\Permissions\Resources\PermissionCollection;
use App\Modules\Account\Permissions\Resources\PermissionResource;
use App\Modules\Base\BaseService;
use Illuminate\Support\Facades\DB;

class PermissionService extends BaseService
{
    public function __construct()
    {
        $this->setModel(Permission::class);
        $this->setResource(PermissionResource::class);
        $this->setCollection(PermissionCollection::class);
    }

    public function getAll()
    {
        return $this->model->groupBy('name')
        ->orderBy('name')
        ->get();
    }

    public function createPermission(
        string $permission_type,
        string $category_type,
        string $permission_name,
        string $category_name,
        bool $multiple = true
    ) {
        try {
            DB::beginTransaction();

            $category = PermissionCategory::firstOrCreate([
                'type'     => $category_type,
                'name'     => $category_name,
                'multiple' => $multiple,
            ]);

            $permission = Permission::firstOrCreate([
                'type'                   => $permission_type,
                'name'                   => $permission_name,
                'permission_category_id' => $category->id
            ]);

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }

        return $permission;
    }
}
