<?php

namespace App\Policies;

use App\Models\User;
use App\Models\UserAddress;

class UserAddressPolicy
{
    /**
     * Create a new policy instance.
     */
    public function own(User $user, UserAddress $address)
    {
        return $address->user_id == $user->id;
    }
}
