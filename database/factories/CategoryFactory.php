<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Category>
 */
class CategoryFactory extends Factory
{
    protected $model = Category::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = $this->faker->sentence(rand(3, 4)); // Judul random 3-7 kata
        $name = rtrim($name, '.'); // Hilangkan titik akhir kalau ada

        return [
            'id'            => fake()->uuid(),               // UUID v4 atau v7 sesuai kebutuhan
            'name'         => $name,
            'slug'          => Str::slug($name),
            'icon'          => 'BookOpen',
            'is_active'     => $this->faker->boolean(99),    
        ];
    }

    /**
     * State untuk buku aktif (default sudah 85%, tapi bisa dipaksa)
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => true,
        ]);
    }

    /**
     * State untuk buku tidak aktif
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}