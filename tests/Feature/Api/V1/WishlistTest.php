<?php

use App\Models\Author;
use App\Models\Wishlist;
use App\Models\WishlistItem;
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


it('can create and fetch wishlist', function () {
    $user = User::factory()->create();
    $book = createBook(['title' => 'Book 1', 'slug' => 'book-1', 'stock' => 20]);

    actingAs($user)
        ->postJson('/api/v1/wishlist', [
            'items' => [
                [
                    'book_id' => $book->id,
                ],
            ],
        ])
        ->assertOk()
        ->assertJsonPath('items.0.book_id', $book->id);

    getJson('/api/v1/wishlist')
        ->assertOk()
        ->assertJsonCount(1, 'items');
});


it('can remove wishlist item', function () {
    $user = User::factory()->create();
    $book = createBook();

    actingAs($user)->postJson('/api/v1/wishlist', [
        'items' => [[
            'book_id' => $book->id,
        ]],
    ]);

    $item = WishlistItem::first();

    actingAs($user)
        ->deleteJson("/api/v1/wishlist/items/{$item->id}")
        ->assertOk()
        ->assertJsonCount(0, 'items');
});
