<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Fiksi', 'slug' => 'fiksi'],
            ['name' => 'Non-Fiksi', 'slug' => 'non-fiksi'],
            ['name' => 'Novel', 'slug' => 'novel'],
            ['name' => 'Remaja', 'slug' => 'remaja'],
            ['name' => 'Dewasa', 'slug' => 'dewasa'],
            ['name' => 'Motivasi', 'slug' => 'motivasi'],
            ['name' => 'Biografi', 'slug' => 'biografi'],
            ['name' => 'Fantasi', 'slug' => 'fantasi'],
            ['name' => 'Romance', 'slug' => 'romance'],
            ['name' => 'Petualangan', 'slug' => 'petualangan'],
        ];

        foreach ($categories as $category) {
            Category::create([
                'id' => Str::uuid(),
                'name' => $category['name'],
                'slug' => $category['slug'],
            ]);
        }
    }
}