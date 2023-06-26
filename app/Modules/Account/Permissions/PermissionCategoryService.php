<?php

namespace App\Modules\Account\Permissions;

class PermissionCategoryService
{
    protected PermissionCategory $model;

    public function __construct(PermissionCategory $model)
    {
        $this->model = $model;
    }

    public function get()
    {
        return $this->model->with(['permissions' => function($query){
            $query->orderBy('name');
        }])
        ->orderBy('multiple')
        ->orderBy('name')
        ->get();
    }
}
