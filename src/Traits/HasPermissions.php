<?php

namespace Encore\OrgRbac\Traits;

use Encore\OrgRbac\Models\Enums\IsAdmin;
use Illuminate\Support\Collection;

trait HasPermissions
{
    /**
     * Check if user has button permission.
     *
     * @param $ability
     * @param array $arguments
     *
     * @return bool
     */
    public function can($ability): bool
    {
        if (empty($ability)) {
            return true;
        }
        if ($this->isAdministrator()) {
            return true;
        }

        return $this->buttonPermissionsByDuty()->pluck('uri')->contains($ability);
    }

    public function cannot($ability): bool
    {
        return !$this->can($ability);
    }


    /**
     * Check if user is administrator.
     *
     * @return mixed
     */
    public function isAdministrator(): bool
    {
        return $this->rolesByDuty->pluck('is_admin')->contains(IsAdmin::YES);
    }

    /**
     * Check if user is administrator.
     *
     * @return mixed
     */
    public function isRootAdministrator(): bool
    {
        return $this->rolesByDuty->pluck('slug')->contains('administrator');
    }

    /**
     * Check if user is $role.
     *
     * @param string $role
     *
     * @return mixed
     */
    public function isRole(string $role): bool
    {
        return $this->roles->pluck('slug')->contains($role);
    }

    /**
     * Check if user in $roles.
     *
     * @param array $roles
     *
     * @return mixed
     */
    public function inRoles(array $roles = []): bool
    {
        return $this->roles->pluck('slug')->intersect($roles)->isNotEmpty();
    }

    /**
     * If visible for roles.
     *
     * @param $roles
     *
     * @return bool
     */
    public function visible(array $roles = []): bool
    {
        if (empty($roles)) {
            return true;
        }

        $roles = array_column($roles, 'slug');

        return $this->inRoles($roles) || $this->isAdministrator();
    }

    /**
     * Detach models from the relationship.
     *
     * @return void
     */
    protected static function bootHasPermissions()
    {
        static::deleting(function ($model) {
            $model->rolesByDuty()->detach();
        });
    }
}
