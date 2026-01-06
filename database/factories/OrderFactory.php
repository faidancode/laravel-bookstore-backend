<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        $subtotal = $this->faker->numberBetween(50000, 500000);
        $shipping = 15000;
        $discount = 0;
        $total = $subtotal + $shipping - $discount;
        $orderId = (string) Str::uuid();
        return [
            'id' => $orderId,
            'order_number' => 'ORD-' . strtoupper(Str::random(8)),
            'user_id' => User::factory(),
            'status' => $this->faker->randomElement(['pending', 'paid', 'shipped', 'completed', 'cancelled']),
            'payment_method' => $this->faker->randomElement(['bank_transfer', 'e_wallet', 'credit_card']),
            'payment_status' => 'unpaid',
            'address_snapshot' => [
                'name' => $this->faker->name,
                'phone' => $this->faker->phoneNumber,
                'city' => $this->faker->city,
                'full_address' => $this->faker->address,
            ],
            'subtotal_cents' => $subtotal,
            'discount_cents' => $discount,
            'shipping_cents' => $shipping,
            'total_cents' => $total,
            'note' => $this->faker->sentence,
            'midtrans_order_id' => $orderId,
            'placed_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    /**
     * State khusus untuk status pending
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
        ]);
    }
}