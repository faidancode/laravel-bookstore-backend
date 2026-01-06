<?php

use App\Models\User;
use App\Models\Address;
use App\Models\Author;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Book;
use App\Models\Category;
use App\Models\Order;
use GuzzleHttp\Promise\Create;
use Illuminate\Foundation\Testing\RefreshDatabase;
use function Pest\Laravel\{actingAs, postJson, assertDatabaseHas};

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->address = Address::factory()->create(['user_id' => $this->user->id]);
    $this->cart = Cart::factory()->create(['user_id' => $this->user->id]);
    
    // Pastikan Author & Category tersedia untuk helper createBook()
    $this->author = Author::factory()->create();
    $this->category = Category::factory()->create();
});

it('can checkout successfully from cart', function () {
    // 1. Setup data spesifik untuk test ini
    $book = createBook(['price_cents' => 50000, 'title' => 'Laravel Guide']);
    CartItem::factory()->create([
        'cart_id' => $this->cart->id,
        'book_id' => $book->id,
        'quantity' => 2
    ]);

    $payload = [
        'address_id' => $this->address->id,
        'note' => 'Tolong bungkus kado'
    ];

    // 2. Eksekusi
    actingAs($this->user)
        ->postJson('/api/v1/orders/checkout', $payload)
        ->assertStatus(201)
        ->assertJsonPath('data.total_cents', 115000); // (2 * 50rb) + 15rb ongkir

    // 3. Verifikasi Database
    // Pastikan cart benar-benar terhapus (karena logika repo clear)
    $this->assertDatabaseMissing('carts', ['id' => $this->cart->id]);
    $this->assertDatabaseMissing('cart_items', ['cart_id' => $this->cart->id]);

    $this->assertDatabaseHas('orders', [
        'user_id' => $this->user->id,
        'status' => 'pending'
    ]);

    $this->assertDatabaseHas('order_items', [
        'title_snapshot' => 'Laravel Guide',
        'unit_price_cents' => 50000,
        'quantity' => 2
    ]);
});

it('fails checkout if cart is empty', function () {
    // Di sini kita tidak menambahkan CartItem apapun, sehingga cart kosong
    
    actingAs($this->user)
        ->postJson('/api/v1/orders/checkout', ['address_id' => $this->address->id])
        ->assertStatus(400)
        ->assertSee('Keranjang belanja Anda kosong.');
});

it('fails checkout if address does not belong to user', function () {
    // 1. Siapkan item (agar tidak kena error 'cart empty' duluan)
    $book = createBook();
    CartItem::factory()->create([
        'cart_id' => $this->cart->id,
        'book_id' => $book->id,
        'quantity' => 1
    ]);

    // 2. Buat alamat milik orang lain
    $otherUserAddress = Address::factory()->create(); 

    // 3. Eksekusi
    actingAs($this->user)
        ->postJson('/api/v1/orders/checkout', ['address_id' => $otherUserAddress->id])
        ->assertStatus(404); // Sesuai perilaku findByIdOrFail di AddressRepo
});

it('fails checkout if book stock is insufficient', function () {
    // 1. Tambahkan buku dengan stok mepet
    $book = createBook(['stock' => 1, 'price_cents' => 10000]);

    // 2. Isi cart hanya dengan buku ini
    CartItem::factory()->create([
        'cart_id' => $this->cart->id,
        'book_id' => $book->id,
        'quantity' => 2 // Beli 2, stok cuma 1
    ]);

    actingAs($this->user)
        ->postJson('/api/v1/orders/checkout', ['address_id' => $this->address->id])
        ->assertStatus(400)
        ->assertSee('Stok buku');
});

it('decrements book stock after successful checkout', function () {
    $initialStock = 10;
    $buyQuantity = 3;
    $book = createBook(['stock' => $initialStock]);

    // Isi cart
    CartItem::factory()->create([
        'cart_id' => $this->cart->id,
        'book_id' => $book->id,
        'quantity' => $buyQuantity
    ]);

    actingAs($this->user)
        ->postJson('/api/v1/orders/checkout', ['address_id' => $this->address->id])
        ->assertStatus(201);

    // Pastikan stok berkurang tepat sesuai buyQuantity
    expect($book->refresh()->stock)->toBe($initialStock - $buyQuantity);
});
