<?php

namespace App\Modules\Account\Users;

use App\Modules\Account\Profiles\Profile;
use App\Modules\Base\Traits\UniquelyIdentifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use UniquelyIdentifiable;
    use SoftDeletes;
    use Authorizable;
    use HasFactory;

    protected $casts = [
        'active' => 'boolean',
    ];

    protected $fillable = [
        'name',
        'email',
        'password',
        'active',
        'profile_id'
    ];

    public function profile()
    {
        return $this->belongsTo(Profile::class);
    }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }
}
