<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

if (!function_exists('center_settings')) {
    function center_settings()
    {
        static $settings = null;

        if ($settings === null && Auth::check() && Auth::user()->role == "admin") {
            $settings = DB::table('center_settings')
                ->where('user_id', Auth::id())
                ->first();
        } else if ($settings === null && Auth::check() && (Auth::user()->role == "parent" || Auth::user()->role == "staff")) {
            $settings = DB::table('center_settings')
                ->where('user_id', Auth::user()->created_by)
                ->first();
        }

        return $settings;
    }
}
