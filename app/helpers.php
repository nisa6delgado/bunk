<?php

use App\Models\Rate;

function rate($date = null)
{
    $date = $date ?? now()->format('Y-m-d');
    
    $rate = Rate::whereDate('created_at', $date)->first();

    if ($rate) {
        return $rate->value;
    }

    return null;
}
