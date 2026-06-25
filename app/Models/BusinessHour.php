<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['day_of_week', 'start_time', 'end_time', 'slot_minutes', 'is_open'])]
class BusinessHour extends Model
{
    protected function casts(): array
    {
        return [
            'start_time' => 'datetime:H:i',
            'end_time'   => 'datetime:H:i',
            'is_open'    => 'boolean',
        ];
    }
}