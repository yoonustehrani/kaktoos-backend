<?php

use App\Attributes\DisplayFa;
use App\Contracts\CustomEnum;
use Illuminate\Support\Carbon;

if (! function_exists('get_auth_user')) {
    /**
     * @return \App\Models\User
     */
    function get_auth_user(): \App\Models\User
    {
        return \Illuminate\Support\Facades\Auth::user();
    }
}

if (! function_exists('get_carbon_datetime')) {
    function get_carbon_datetime(string $parto_datetime): Carbon
    {
        return Carbon::createFromFormat('Y-m-d\TH:i:s', $parto_datetime);
    }
}
