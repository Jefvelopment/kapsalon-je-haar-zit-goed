<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[Fillable(['name', 'price', 'duration_minutes'])]
class Treatment extends Model
{
    use HasFactory;

    public function appointments(): BelongsToMany
    {
        return $this->belongsToMany(Appointment::class, 'appointments_treatments')
            ->withTimestamps();
    }
}