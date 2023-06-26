<?php

namespace App\Modules\Account\Permissions;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    public function __construct(PermissionService $service)
    {
        $this->service = $service;
    }

    public function getById($id)
    {
        return response()->json([
            'error'      => false,
            'permission' => $this->service->getById($id),
        ]);
    }

    public function get(Request $request)
    {
        return response()->json([
            'error'       => false,
            'permissions' => $this->service->getAll()
        ]);
    }
}
