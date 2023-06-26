<?php

namespace App\Modules\Account\Permissions;

use App\Modules\Account\Permissions\Database\Factories\PermissionFactory;
use App\Modules\Base\Traits\UniquelyIdentifiable;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    use UniquelyIdentifiable;
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'name',
        'type',
        'permission_category_id',
    ];

    public function permission_category()
    {
        return $this->belongsTo(PermissionCategory::class);
    }

    public function permissionCategory()
    {
        return $this->belongsTo(PermissionCategory::class);
    }
}
