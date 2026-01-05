<?php

namespace Database\Factories;

use App\Models\Wishlist;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class WishlistFactory extends Factory
{
    protected $model = Wishlist::class;

    public function definition(): array
    {
        return [
            'id' => (string) fake()->uuid(),
            'user_id' => User::factory(),
        ];
    }
}
