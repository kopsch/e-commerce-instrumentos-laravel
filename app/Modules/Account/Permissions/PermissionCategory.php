<?php

namespace App\Modules\Account\Permissions;

use App\Modules\Account\Permissions\Database\Factories\PermissionCategoryFactory;
use App\Modules\Base\Traits\UniquelyIdentifiable;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PermissionCategory extends Model
{
    use UniquelyIdentifiable;
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'name',
        'type'
    ];

    public function permissions()
    {
        return $this->hasMany(Permission::class);
    }
}
