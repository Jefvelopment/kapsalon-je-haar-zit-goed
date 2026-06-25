<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'price', 'stock', 'category', 'image_path'])]
class Product extends Model
{
    use HasFactory;

    public function orders(): BelongsToMany
    {
        return $this->belongsToMany(Order::class, 'orders_products')
            ->withPivot('quantity')
            ->withTimestamps();
    }

    public function views(): HasMany
    {
        return $this->hasMany(ProductView::class);
    }

    public function imageUrl(): string
    {
        if ($this->image_path) {
            return \Illuminate\Support\Facades\Storage::url($this->image_path);
        }

        return asset('images/product-placeholder.png');
    }
}