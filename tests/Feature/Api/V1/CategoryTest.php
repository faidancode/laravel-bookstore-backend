<?php

use App\Models\Author;
use App\Models\Book;
use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
});

describe('GET /api/v1/categories', function () {
    it('can list all categories', function () {
        $createCategories = Category::factory()->count(5)->create(['is_active' => true]);
        $response = $this->getJson('/api/v1/categories');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'ok',
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'slug',
                        'icon',
                    ],
                ],
                'meta' => [
                    'page',
                    'pageSize',
                    'total',
                    'totalPages',
                ],
                'error',
            ])
            ->assertJson([
                'ok' => true,
                'meta' => [
                    'page' => 1,
                    'total' => 5,
                ],
            ]);
    });

    it('returns empty array when no categories exist', function () {
        $response = $this->getJson('/api/v1/categories');

        $response->assertStatus(200)
            ->assertJson([
                'ok' => true,
                'data' => [],
                'meta' => [
                    'total' => 0,
                ],
            ]);
    });

    it('can paginate categories', function () {
        Category::factory()->count(25)->create(['is_active' => true]);

        $response = $this->getJson('/api/v1/categories?page=2&pageSize=10');

        $response->assertStatus(200)
            ->assertJson([
                'ok' => true,
                'meta' => [
                    'page' => 2,
                    'pageSize' => 10,
                    'total' => 25,
                    'totalPages' => 3,
                ],
            ])
            ->assertJsonCount(10, 'data');
    });

    it('only shows active categories', function () {
        Category::factory()->count(3)->create(['is_active' => true]);
        Category::factory()->count(2)->create(['is_active' => false]);

        $response = $this->getJson('/api/v1/categories');

        $response->assertStatus(200)
            ->assertJson([
                'ok' => true,
                'meta' => [
                    'total' => 3,
                ],
            ]);
    });

    it('defaults to sorting by sort_order', function () {
        Category::factory()->create([
            'name' => 'Category B',
            'sort_order' => 2,
            'is_active' => true,
        ]);

        Category::factory()->create([
            'name' => 'Category A',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        $response = $this->getJson('/api/v1/categories');

        $response->assertStatus(200);

        $categories = $response->json('data');
        expect($categories[0]['name'])->toBe('Category A')
            ->and($categories[1]['name'])->toBe('Category B');
    });

    it('validates page parameter', function () {
        $response = $this->getJson('/api/v1/categories?page=0');

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['page']);
    });

    it('validates pageSize max limit', function () {
        $response = $this->getJson('/api/v1/categories?pageSize=150');

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['pageSize']);
    });

    it('validates sort parameter', function () {
        $response = $this->getJson('/api/v1/categories?sort=invalid_field');

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['sort']);
    });
});

describe('GET /api/v1/categories/{slug}/books', function () {
    beforeEach(function () {
        $this->category = Category::factory()->create([
            'name' => 'Novel',
            'slug' => 'novel',
            'is_active' => true,
        ]);

        $this->author = Author::factory()->create();
    });

    it('can show category with books', function () {
        Book::factory()->count(5)->create([
            'category_id' => $this->category->id,
            'author_id' => $this->author->id,
            'is_active' => true,
        ]);

        $response = $this->getJson('/api/v1/categories/novel/books');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'ok',
                'data' => [
                    'category' => [
                        'id',
                        'name',
                        'slug',
                    ],
                    'books' => [
                        '*' => [
                            'id',
                            'title',
                            'slug',
                            'price',
                        ],
                    ],
                ],
                'meta' => [
                    'page',
                    'pageSize',
                    'total',
                    'totalPages',
                ],
                'error',
            ])
            ->assertJson([
                'ok' => true,
                'data' => [
                    'category' => [
                        'slug' => 'novel',
                    ],
                ],
                'meta' => [
                    'total' => 5,
                ],
            ]);
    });

    it('returns 404 when category not found', function () {
        $response = $this->getJson('/api/v1/categories/non-existent/books');

        $response->assertStatus(404)
            ->assertJson([
                'ok' => false,
                'data' => null,
                'error' => [
                    'message' => 'Category not found',
                    'code' => 'CATEGORY_NOT_FOUND',
                ],
            ]);
    });

    it('can search books in category', function () {
        Book::factory()->create([
            'category_id' => $this->category->id,
            'author_id' => $this->author->id,
            'title' => 'Bumi Manusia',
            'is_active' => true,
        ]);

        Book::factory()->create([
            'category_id' => $this->category->id,
            'author_id' => $this->author->id,
            'title' => 'Laskar Pelangi',
            'is_active' => true,
        ]);

        $response = $this->getJson('/api/v1/categories/novel/books?q=Bumi');

        $response->assertStatus(200)
            ->assertJson([
                'ok' => true,
                'meta' => [
                    'total' => 1,
                ],
            ]);
    });

    it('can filter books by author', function () {
        $anotherAuthor = Author::factory()->create();

        Book::factory()->count(3)->create([
            'category_id' => $this->category->id,
            'author_id' => $this->author->id,
            'is_active' => true,
        ]);

        Book::factory()->count(2)->create([
            'category_id' => $this->category->id,
            'author_id' => $anotherAuthor->id,
            'is_active' => true,
        ]);

        $response = $this->getJson("/api/v1/categories/novel/books?authorId={$anotherAuthor->id}");

        $response->assertStatus(200)
            ->assertJson([
                'ok' => true,
                'meta' => [
                    'total' => 2,
                ],
            ]);
    });

    it('can filter books by price range', function () {
        Book::factory()->create([
            'category_id' => $this->category->id,
            'author_id' => $this->author->id,
            'price_cents' => 5000000,
            'is_active' => true,
        ]);

        Book::factory()->create([
            'category_id' => $this->category->id,
            'author_id' => $this->author->id,
            'price_cents' => 15000000,
            'is_active' => true,
        ]);

        $response = $this->getJson('/api/v1/categories/novel/books?minPrice=4000000&maxPrice=10000000');

        $response->assertStatus(200)
            ->assertJson([
                'ok' => true,
                'meta' => [
                    'total' => 1,
                ],
            ]);
    });

    it('can paginate books in category', function () {
        Book::factory()->count(25)->create([
            'category_id' => $this->category->id,
            'author_id' => $this->author->id,
            'is_active' => true,
        ]);

        $response = $this->getJson('/api/v1/categories/novel/books?page=2&pageSize=10');

        $response->assertStatus(200)
            ->assertJson([
                'ok' => true,
                'meta' => [
                    'page' => 2,
                    'pageSize' => 10,
                    'total' => 25,
                    'totalPages' => 3,
                ],
            ]);
    });

    it('only shows active books by default', function () {
        Book::factory()->create([
            'category_id' => $this->category->id,
            'author_id' => $this->author->id,
            'is_active' => true,
        ]);

        Book::factory()->create([
            'category_id' => $this->category->id,
            'author_id' => $this->author->id,
            'is_active' => false,
        ]);

        $response = $this->getJson('/api/v1/categories/novel/books');

        $response->assertStatus(200)
            ->assertJson([
                'ok' => true,
                'meta' => [
                    'total' => 1,
                ],
            ]);
    });

    it('can sort books by price', function () {
        Book::factory()->create([
            'category_id' => $this->category->id,
            'author_id' => $this->author->id,
            'title' => 'Expensive Book',
            'price_cents' => 15000000,
            'is_active' => true,
        ]);

        Book::factory()->create([
            'category_id' => $this->category->id,
            'author_id' => $this->author->id,
            'title' => 'Cheap Book',
            'price_cents' => 5000000,
            'is_active' => true,
        ]);

        $response = $this->getJson('/api/v1/categories/novel/books?sort=price&order=asc');

        $response->assertStatus(200);

        $books = $response->json('data.books');
        expect($books[0]['title'])->toBe('Cheap Book')
            ->and($books[1]['title'])->toBe('Expensive Book');
    });

    it('returns 404 for inactive category', function () {
        $inactiveCategory = Category::factory()->create([
            'slug' => 'inactive-category',
            'is_active' => false,
        ]);

        $response = $this->getJson('/api/v1/categories/inactive-category/books');

        $response->assertStatus(404);
    });
});
