<?php

namespace App\Utils;

use Auth;

trait Permissions
{
    /**
     * Только для администратора
     *
     * @return bool
     */
    public function isAdmin(): bool
    {
        $role = Auth::user()->role;

        return $role == 1;
    }

    /**
     * Для администратора и менеджера
     *
     * @return bool
     */
    public function isManager(): bool
    {
        $role = Auth::user()->role;

        return $role == 1 || $role == 2;
    }
}
