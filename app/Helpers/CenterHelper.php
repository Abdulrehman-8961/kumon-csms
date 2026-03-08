<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

if (!function_exists('center_settings')) {
    function center_settings()
    {
        static $settings = null;

        if ($settings === null && Auth::check()) {
            $settings = DB::table('center_settings')
                ->where('user_id', Auth::id())
                ->first();
        }

        return $settings;
    }
}
