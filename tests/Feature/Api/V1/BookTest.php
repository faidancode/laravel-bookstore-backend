<?php

/** @property \App\Models\Category $category */
/** @property \App\Models\Author   $author   */
/** @property \App\Models\User     $user     */

use App\Models\Author;
use App\Models\Book;
use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use function Pest\Laravel\{actingAs, getJson, postJson, patchJson, deleteJson};

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->category = Category::create([
        'id'   => fake()->uuid(),
        'name' => 'Novel',
        'slug' => 'novel',
    ]);

    $this->author = Author::create([
        'id'   => fake()->uuid(),
        'name' => 'Tere Liye',
        'bio'  => 'Penulis terkenal Indonesia',
    ]);

    $this->user = User::factory()->create();
});

/**
 * Helper untuk membuat book dengan default value yang umum dipakai di test
 */
function createBook(array $overrides = []): Book
{
    $book = Book::factory()
        ->for(test()->category, 'category')
        ->for(test()->author, 'author')
        ->state(['is_active' => true])
        ->create($overrides);
    return $book;
}

describe('GET /api/v1/books', function () {

    it('lists all active books', function () {
        createBook(['title' => 'Book 1', 'slug' => 'book-1']);
        createBook(['title' => 'Book 2', 'slug' => 'book-2']);
        createBook(['title' => 'Book 3', 'slug' => 'book-3', 'is_active' => false]);

        getJson('/api/v1/books')
            ->assertOk()
            ->assertJson([
                'ok' => true,
                'meta' => ['total' => 2],
            ])
            ->assertJsonCount(2, 'data');
    });

    it('returns empty array when no books exist', function () {
        getJson('/api/v1/books')
            ->assertOk()
            ->assertJson([
                'ok' => true,
                'data' => [],
                'meta' => ['total' => 0],
            ]);
    });

    it('supports pagination', function () {
        Book::factory()->count(25)->create([
            'category_id' => $this->category->id,
            'author_id'   => $this->author->id,
            'is_active'   => true,
        ]);

        getJson('/api/v1/books?page=2&pageSize=10')
            ->assertOk()
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

    it('filters by category slug', function () {
        $fiction = Category::create([
            'id'   => fake()->uuid(),
            'name' => 'Fiction',
            'slug' => 'fiction',
        ]);

        createBook(); // novel
        createBook(['category_id' => $fiction->id, 'slug' => 'fiction-book-1']);
        createBook(['category_id' => $fiction->id, 'slug' => 'fiction-book-2']);

        getJson('/api/v1/books?category=fiction')
            ->assertOk()
            ->assertJson([
                'ok' => true,
                'meta' => ['total' => 2],
            ]);
    });

    it('filters by price range', function () {
        createBook(['price_cents' =>  5000000]); //  50k
        createBook(['price_cents' => 10000000]); // 100k
        createBook(['price_cents' => 15000000]); // 150k

        getJson('/api/v1/books?minPrice=7000000&maxPrice=12000000')
            ->assertOk()
            ->assertJsonPath('meta.total', 1);
    });

    it('swaps min & max price when min > max', function () {
        createBook(['price_cents' => 10000000]);

        getJson('/api/v1/books?minPrice=15000000&maxPrice=5000000')
            ->assertOk()
            ->assertJsonPath('meta.total', 1);
    });

    it('searches by title', function () {
        createBook(['title' => 'Bumi Manusia']);
        createBook(['title' => 'Laskar Pelangi']);

        getJson('/api/v1/books?search=Bumi')
            ->assertOk()
            ->assertJsonPath('meta.total', 1);
    });

    it('sorts by price ascending', function () {
        createBook(['title' => 'Expensive', 'price_cents' => 15000000]);
        createBook(['title' => 'Cheap',     'price_cents' =>  5000000]);

        $response = getJson('/api/v1/books?sort=price&order=asc');

        $response->assertOk();
        expect($response->json('data.0.title'))->toBe('Cheap');
        expect($response->json('data.1.title'))->toBe('Expensive');
    });

    it('only shows active books by default', function () {
        createBook(); // active
        createBook(['is_active' => false]);

        getJson('/api/v1/books')
            ->assertOk()
            ->assertJsonPath('meta.total', 1);
    });

    it('validates invalid page parameter', function () {
        getJson('/api/v1/books?page=0')
            ->assertUnprocessable()
            ->assertJsonValidationErrors('page');
    });

    it('validates pageSize exceeds max limit', function () {
        getJson('/api/v1/books?pageSize=150')
            ->assertUnprocessable()
            ->assertJsonValidationErrors('pageSize');
    });

    it('validates invalid sort field', function () {
        getJson('/api/v1/books?sort=invalid')
            ->assertUnprocessable()
            ->assertJsonValidationErrors('sort');
    });
});

describe('GET /api/v1/books/{slug}', function () {

    it('shows book detail by slug', function () {
        $book = createBook([
            'title' => 'Bumi Manusia',
            'slug'  => 'bumi-manusia',
        ]);

        getJson("/api/v1/books/{$book->slug}")
            ->assertOk()
            ->assertJson([
                'ok' => true,
                'data' => [
                    'id'    => $book->id,
                    'title' => 'Bumi Manusia',
                    'slug'  => 'bumi-manusia',
                ],
            ])
            ->assertJsonStructure([
                'ok',
                'data' => [
                    'id',
                    'title',
                    'slug',
                    'description',
                    'author',
                    'category',
                ],
                'meta' => ['averageRating', 'totalReviews'],
                'error',
            ]);
    });

    it('returns 404 for non-existent book', function () {
        getJson('/api/v1/books/non-existent')
            ->assertNotFound()
            ->assertJson([
                'ok' => false,
                'data' => null,
                'error' => [
                    'message' => 'Book not found',
                    'code'    => 'BOOK_NOT_FOUND',
                ],
            ]);
    });

    it('returns 404 for inactive book', function () {
        createBook([
            'slug'      => 'inactive-book',
            'is_active' => false,
        ]);

        getJson('/api/v1/books/inactive-book')
            ->assertNotFound();
    });
});
