<?php

namespace App\Policies\Definitions;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use App\Models\Definitions\Region as RegionDefinition;

class RegionPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can index definitions.
     *
     * @param  User  $user
     * @return boolean
     */
    public function index(User $user)
    {
        return true;
    }

    /**
     * Determine whether the user can view the definition.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Definitions\Region  $definition
     * @return boolean
     */
    public function read(User $user, RegionDefinition $definition)
    {
        return true;
    }

    /**
     * Determine whether the user can create definitions.
     *
     * @param  \App\Models\User  $user
     * @return boolean
     */
    public function create(User $user)
    {
        return false;
    }

    /**
     * Determine whether the user can update the definition.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Definitions\Region  $definition
     * @return boolean
     */
    public function update(User $user, RegionDefinition $definition)
    {
        return false;
    }

    /**
     * Determine whether the user can delete the definition.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Definitions\Region  $definition
     * @return boolean
     */
    public function delete(User $user, RegionDefinition $definition)
    {
        return false;
    }
}
