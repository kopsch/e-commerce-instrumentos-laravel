<?php

namespace App\Modules\Account\Permissions;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PermissionCategoryController extends Controller
{
    private PermissionCategoryService $service;

    public function __construct(PermissionCategoryService $service)
    {
        $this->service = $service;
    }

    public function get(Request $request)
    {
        return response()->json([
            'data' => [
                'permissionCategories' => $this->service->get()
            ]
        ]);
    }
}
