<?php

namespace Database\Factories;

use App\Models\Wishlist;
use App\Models\WishlistItem;
use App\Models\Book;
use Illuminate\Database\Eloquent\Factories\Factory;

class WishlistItemFactory extends Factory
{
    protected $model = WishlistItem::class;

    public function definition(): array
    {
        return [
            'id' => fake()->uuid(),
            'wishlist_id' => Wishlist::factory(),
            'book_id' => Book::factory(),
        ];
    }
}
