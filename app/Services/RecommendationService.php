<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductView;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

class RecommendationService
{

    public function recommendedProducts(int $limit = 4): Collection
    {
        $userId = Auth::id();

        if (!$userId) {
            return $this->fallbackPopularProducts($limit);
        }

        $viewedProductIds = ProductView::where('user_id', $userId)
            ->pluck('product_id')
            ->unique();

        if ($viewedProductIds->isEmpty()) {
            return $this->fallbackPopularProducts($limit);
        }

        $viewedCategories = Product::whereIn('id', $viewedProductIds)
            ->pluck('category')
            ->filter()
            ->unique();

        if ($viewedCategories->isEmpty()) {
            return $this->fallbackPopularProducts($limit);
        }

        $recommended = Product::whereIn('category', $viewedCategories)
            ->whereNotIn('id', $viewedProductIds)
            ->inRandomOrder()
            ->limit($limit)
            ->get();

        if ($recommended->count() < $limit) {
            $extra = $this->fallbackPopularProducts($limit - $recommended->count(), $recommended->pluck('id'));
            $recommended = $recommended->concat($extra);
        }

        return $recommended;
    }

    protected function fallbackPopularProducts(int $limit, ?Collection $excludeIds = null): Collection
    {
        $query = Product::query()
            ->withCount('views')
            ->orderByDesc('views_count');

        if ($excludeIds && $excludeIds->isNotEmpty()) {
            $query->whereNotIn('id', $excludeIds);
        }

        return $query->limit($limit)->get();
    }
}