<?php

namespace App\Http\Controllers;

abstract class Controller
{
    /**
     * Get the authenticated user.
     *
     * @return \App\Models\User
     */
    protected function user()
    {
        return auth()->user(); // @phpstan-ignore-line
    }
}
