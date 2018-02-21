<?php

namespace Orchestra\Model\Traits;

use Orchestra\Model\Role;
use Illuminate\Contracts\Support\Arrayable;

trait CheckRoles
{
    /**
     * Determine if current user has the given role.
     *
     * @param  \Illuminate\Contracts\Support\Arrayable|array|string  $roles
     *
     * @return bool
     */
    public function hasRoles($roles): bool
    {
        $userRoles = $this->getRoles();

        if ($userRoles instanceof Arrayable) {
            $userRoles = $userRoles->toArray();
        }

        // For a pre-caution, we should return false in events where user
        // roles not an array.
        if (! is_array($userRoles)) {
            return false;
        }

        // We should ensure that all given roles match the current user,
        // consider it as a AND condition instead of OR.
        foreach ((array) $roles as $role) {
            if (! in_array($role, $userRoles)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Determine if current user has any of the given role.
     *
     * @param  \Illuminate\Contracts\Support\Arrayable|array|string  $roles
     *
     * @return bool
     */
    public function hasAnyRoles($roles): bool
    {
        $userRoles = $this->getRoles();

        if ($userRoles instanceof Arrayable) {
            $userRoles = $userRoles->toArray();
        }

        // For a pre-caution, we should return false in events where user
        // roles not an array.
        if (! is_array($userRoles)) {
            return false;
        }

        // We should ensure that any given roles match the current user,
        // consider it as OR condition.
        foreach ((array) $roles as $role) {
            if (in_array($role, $userRoles)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Assign roles to user.
     *
     * @param  \Orchestra\Model\Role|int|array  $roles
     *
     * @return $this
     */
    public function attachRoles($roles): self
    {
        if ($roles instanceof Role) {
            $roles = [$roles->getKey()];
        }

        $this->roles()->sync($roles, false);

        unset($this->relations['roles']);

        return $this;
    }

    /**
     * Un-assign roles from user.
     *
     * @param  \Orchestra\Model\Role|int|array  $roles
     *
     * @return $this
     */
    public function detachRoles($roles): self
    {
        if ($roles instanceof Role) {
            $roles = [$roles->getKey()];
        }

        $this->roles()->detach($roles);

        unset($this->relations['roles']);

        return $this;
    }

    /**
     * Get roles name as an array.
     *
     * @return \Illuminate\Support\Collection
     */
    abstract public function getRoles();
}