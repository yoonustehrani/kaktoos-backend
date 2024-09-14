<?php

use App\Attributes\DisplayFa;
use App\Contracts\CustomEnum;

if (! function_exists('get_auth_user')) {
    /**
     * @return \App\Models\User
     */
    function get_auth_user(): \App\Models\User
    {
        return \Illuminate\Support\Facades\Auth::user();
    }
}