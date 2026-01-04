<?php

use App\Models\Author;
use App\Models\Book;
use App\Models\Cart;
use App\Models\CartItem;
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


it('can create and fetch cart', function () {
    $user = User::factory()->create();
    $category = Category::factory()->create();
    $book = createBook(['title' => 'Book 1', 'slug' => 'book-1', 'stock' => 20]);

    actingAs($user)
        ->postJson('/api/v1/cart', [
            'items' => [
                [
                    'book_id' => $book->id,
                    'category_id' => $category->id,
                    'quantity' => 2,
                    'price_cents_at_add' => 15000,
                ],
            ],
        ])
        ->assertOk()
        ->assertJsonPath('items.0.quantity', 2);

    getJson('/api/v1/cart')
        ->assertOk()
        ->assertJsonCount(1, 'items');
});

it('prevents updating another user cart item', function () {
    $user = User::factory()->create();
    $other = User::factory()->create();
    $book = createBook();

    $cart = Cart::factory()->for($user)->create();
    $item = CartItem::factory()->for($cart)->create(['book_id' => $book->id,'quantity' => 1]);

    actingAs($other)
        ->patchJson("/api/v1/cart/items/{$item->id}", ['quantity' => 2])
        ->assertForbidden();
});

it('can increment cart item quantity', function () {
    $user = User::factory()->create();
    $book = createBook(['stock' => 10]);

    actingAs($user)->postJson('/api/v1/cart', [
        'items' => [[
            'book_id' => $book->id,
            'quantity' => 1,
            'price_cents_at_add' => 15000,
        ]],
    ]);

    $item = CartItem::first();

    actingAs($user)
        ->patchJson("/api/v1/cart/items/{$item->id}", ['quantity' => 3])
        ->assertOk()
        ->assertJsonPath('items.0.quantity', 3);
});

it('can decrement cart item quantity', function () {
    $user = User::factory()->create();
    $book = createBook();

    actingAs($user)->postJson('/api/v1/cart', [
        'items' => [[
            'book_id' => $book->id,
            'quantity' => 2,
            'price_cents_at_add' => 15000,
        ]],
    ]);

    $item = CartItem::first();

    actingAs($user)
        ->postJson("/api/v1/cart/items/{$item->id}/decrement")
        ->assertOk()
        ->assertJsonPath('items.0.quantity', 1);
});

it('removes item when decrementing from quantity 1', function () {
    $user = User::factory()->create();
    $book = createBook();

    actingAs($user)->postJson('/api/v1/cart', [
        'items' => [[
            'book_id' => $book->id,
            'quantity' => 1,
            'price_cents_at_add' => 15000,
        ]],
    ]);

    $item = CartItem::first();

    actingAs($user)
        ->postJson("/api/v1/cart/items/{$item->id}/decrement")
        ->assertOk()
        ->assertJsonCount(0, 'items');

    expect(CartItem::count())->toBe(0);
});

it('can update cart item quantity directly', function () {
    $user = User::factory()->create();
    $book = createBook(['stock' => 10]);

    actingAs($user)->postJson('/api/v1/cart', [
        'items' => [[
            'book_id' => $book->id,
            'quantity' => 1,
            'price_cents_at_add' => 15000,
        ]],
    ]);

    $item = CartItem::first();

    actingAs($user)
        ->patchJson("/api/v1/cart/items/{$item->id}", ['quantity' => 5])
        ->assertOk()
        ->assertJsonPath('items.0.quantity', 5);
});

it('can remove cart item', function () {
    $user = User::factory()->create();
    $book = createBook();

    actingAs($user)->postJson('/api/v1/cart', [
        'items' => [[
            'book_id' => $book->id,
            'quantity' => 2,
            'price_cents_at_add' => 15000,
        ]],
    ]);

    $item = CartItem::first();

    actingAs($user)
        ->deleteJson("/api/v1/cart/items/{$item->id}")
        ->assertOk()
        ->assertJsonCount(0, 'items');
});
