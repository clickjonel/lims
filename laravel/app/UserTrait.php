<?php

namespace App;

use App\Models\User;

trait UserTrait
{
    public function isPermanent(User $user)
    {
        return $user->assignment->employee_status_id === 1;
    }
}
