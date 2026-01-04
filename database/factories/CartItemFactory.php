<?php

namespace Database\Factories;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Book;
use Illuminate\Database\Eloquent\Factories\Factory;

class CartItemFactory extends Factory
{
    protected $model = CartItem::class;

    public function definition(): array
    {
        return [
            'id' => fake()->uuid(),
            'cart_id' => Cart::factory(),
            'book_id' => Book::factory(),
            'quantity' => 1,
            'price_cents_at_add' => 15000,
        ];
    }
}
