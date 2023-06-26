<?php

namespace App\Modules\Base;

use Illuminate\Foundation\Auth\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class BasePolicy
{
    use HandlesAuthorization;

    protected string $categoryName;

    protected function can(User $user, string $type, string $category)
    {
        if (empty($user->profile)) {
            return false;
        }

        $permission = $user->profile->permissions()
            ->where('type', $type)
            ->whereHas('permission_category', function ($query) use ($category) {
                $query->where('type', $category);
            })
            ->first();

        if (empty($permission)) {
            return false;
        }

        return $permission->pivot->allow;
    }

    public function viewAny(User $user)
    {
        return $this->can($user, 'view', $this->categoryName);
    }

    public function view(User $user)
    {
        return $this->can($user, 'view', $this->categoryName);
    }

    public function create(User $user)
    {
        return $this->can($user, 'store', $this->categoryName);
    }

    public function update(User $user)
    {
        return $this->can($user, 'update', $this->categoryName);
    }

    public function delete(User $user)
    {
        return $this->can($user, 'delete', $this->categoryName);
    }

    public function restore(User $user)
    {
        return $this->can($user, 'restore', $this->categoryName);
    }
}
