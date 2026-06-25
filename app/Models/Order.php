<?php

namespace App\Models;

use App\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[Fillable(['user_id', 'guest_name', 'guest_email', 'guest_phone', 'date', 'status', 'price'])]
class Order extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'date'   => 'date',
            'status' => OrderStatus::class,
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'orders_products')
            ->withPivot('quantity')
            ->withTimestamps();
    }

 
    public function customerName(): string
    {
        return $this->user?->name ?? $this->guest_name ?? 'Onbekend';
    }

    
    public function customerEmail(): ?string
    {
        return $this->user?->email ?? $this->guest_email;
    }
}