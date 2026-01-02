<?php

namespace Database\Factories;

use App\Models\Book;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Book>
 */
class BookFactory extends Factory
{
    protected $model = Book::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = $this->faker->sentence(rand(3, 4)); // Judul random 3-7 kata
        $title = rtrim($title, '.'); // Hilangkan titik akhir kalau ada

        return [
            'id'            => fake()->uuid(),               // UUID v4 atau v7 sesuai kebutuhan
            'title'         => $title,
            'slug'          => Str::slug($title),
            'cover_url'     => "https://res.cloudinary.com/dersjymlc/image/upload/v1766913324/mistborn_uw0mmt.webp",
            'isbn'          => $this->faker->unique()->isbn13, // ISBN-13 unik
            'price_cents'   => $this->faker->numberBetween(2500000, 25000000), // 25rb - 250rb (dalam sen)
            'stock'         => $this->faker->numberBetween(0, 50),
            'is_active'     => $this->faker->boolean(85),    // 85% kemungkinan active
            'author_id'     => null,                         // Akan diisi via relationship atau override
            'category_id'   => null,                         // Sama seperti author_id
            'description'   => $this->faker->paragraphs(3, true), // Opsional: deskripsi panjang
            'published_at'  => $this->faker->dateTimeBetween('-5 years', 'now'), // Tanggal terbit
            // Tambahkan field lain jika ada di model migration kamu, misal:
            // 'cover_image' => $this->faker->imageUrl(640, 960, 'book', true),
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

    /**
     * State dengan harga tertentu (untuk test filter price)
     */
    public function withPrice(int $cents): static
    {
        return $this->state(fn (array $attributes) => [
            'price_cents' => $cents,
        ]);
    }

    /**
     * Contoh state dengan judul tertentu (untuk test search)
     */
    public function titled(string $title): static
    {
        return $this->state(fn (array $attributes) => [
            'title' => $title,
            'slug'  => Str::slug($title),
        ]);
    }
}