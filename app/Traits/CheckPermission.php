<?php

namespace App\Traits;

use Auth;

trait CheckPermission
{

    /**
     * @param int $user_id
     * @param int $permissions
     * @return bool
     */
    private function checkAuthUserPermission(int $user_id, int $permissions = 0) : bool
    {
        if($user_id === Auth::user()->id || $permissions == 1)
            return true;
        else
            return false;
    }
}