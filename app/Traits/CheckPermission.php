<?php

namespace App\Traits;

use Auth;

trait CheckPermission
{

    /**
     * @param int $user_id
     * @return bool
     */
    private function checkAuthUserPermission(int $user_id) : bool
    {
        if($user_id === Auth::user()->id)
            return true;
        else
            return false;
    }
}