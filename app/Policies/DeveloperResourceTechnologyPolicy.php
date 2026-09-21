<?php

namespace App\Policies;

use App\Models\DeveloperResourceTechnology;
use App\Models\User;

class DeveloperResourceTechnologyPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $this->canManage($user);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, DeveloperResourceTechnology $developerResourceTechnology): bool
    {
        return $this->canManage($user);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $this->canManage($user);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, DeveloperResourceTechnology $developerResourceTechnology): bool
    {
        return $this->canManage($user);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, DeveloperResourceTechnology $developerResourceTechnology): bool
    {
        return $this->canManage($user);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, DeveloperResourceTechnology $developerResourceTechnology): bool
    {
        return $this->canManage($user);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, DeveloperResourceTechnology $developerResourceTechnology): bool
    {
        return $this->canManage($user);
    }

    private function canManage(User $user): bool
    {
        return $user->hasAnyRole(['super_admin', 'admin', 'editor']);
    }
}
