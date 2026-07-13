<?php

use App\Models\Rate;

function rate()
{
    $rate = Rate::whereDate('created_at', now()->format('Y-m-d'))->first();

    if ($rate) {
        return $rate->value;
    }

    return null;
}
