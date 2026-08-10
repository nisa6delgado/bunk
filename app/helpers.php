<?php

use App\Models\Rate;
use App\Models\Setting;

function color()
{
    $setting = Setting::where('key', 'color')->first();
    return $setting->value;
}

function rate($date = null)
{
    $date = $date ?? now()->format('Y-m-d');
    
    $rate = Rate::whereDate('created_at', $date)->first();

    if ($rate) {
        return $rate->value;
    }

    return null;
}
