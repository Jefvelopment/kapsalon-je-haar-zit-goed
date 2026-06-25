<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['date', 'day_of_week', 'start_time', 'end_time', 'is_recurring', 'reason', 'created_by'])]
class ScheduleBlock extends Model
{
    protected function casts(): array
    {
        return [
            'date'         => 'date',
            'start_time'   => 'datetime:H:i',
            'end_time'     => 'datetime:H:i',
            'is_recurring' => 'boolean',
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}