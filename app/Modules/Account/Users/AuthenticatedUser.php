<?php

namespace App\Modules\Account\Users;

use App\Modules\Account\Exceptions\AccountException;
use Illuminate\Support\Facades\DB;

class AuthenticatedUser
{
    private string $id;
    private string $name;
    private string $email;
    private array $profile;
    public function __construct(string $userId)
    {
        $this->id = $userId;
        $this->setQueryResult();
        $this->setUser();
    }

    public function toArray()
    {
        return [
            'id'               => $this->id,
            'name'             => $this->name,
            'email'            => $this->email,
            'profile'          => $this->profile,
        ];
    }

    private function setQueryResult()
    {
        $this->queryResult = DB::select("SELECT
                user.id         AS user_id,
                user.profile_id AS profile_id,
                user.name       AS user_name,
                user.email      AS user_email
            FROM users user
            WHERE user.id = :userId
            AND user.deleted_at IS NULL
            GROUP BY st.id
            ", ['userId' => $this->id]
        );

        if (empty($this->queryResult)) {
            throw new AccountException(404, __('users::toasts.user_not_found'));
        }
    }

    private function setUser()
    {
        $this->setBasicInfo();
        $this->setAuthenticatableInfo();
    }

    private function setBasicInfo()
    {
        $this->name    = $this->queryResult[0]->user_name;
        $this->email   = $this->queryResult[0]->user_email;
        $this->profile = [ 'permissions' => $this->getPermissions() ];
    }

    private function getPermissions()
    {
        $profileId = $this->queryResult[0]->profile_id;

        $query = DB::select(
            "SELECT
                permission.type AS permission_type,
                pp.allow        AS allow,
                category.type   AS category_type
            FROM profiles profile
            INNER JOIN permission_profile pp ON pp.profile_id = profile.id
            INNER JOIN permissions permission ON permission.id = pp.permission_id
            INNER JOIN permission_categories category ON permission.permission_category_id = category.id
            WHERE profile.id = :profileId
            ", ['profileId' => $profileId]
        );

        return array_map(fn ($item) => [
            'type'                => $item->permission_type,
            'permission_category' => [
                'type' => $item->category_type
            ],
            'allow' => (bool) $item->allow,
        ], $query);
    }

    private function getAccessType()
    {
        $permissions = $this->profile['permissions'];

        foreach ($permissions as $permission) {
            if ($permission['permission_category'] == 'access_type') {
                return $permission['type'];
            }
        }

        return null;
    }
}
