<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\User;

abstract class Controller
{
    protected function user(): User
    {
        /** @var User $user */
        $user = auth()->user();

        return $user;
    }
}
