<?php

use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use function Pest\Laravel\{actingAs, getJson, postJson,assertDatabaseHas};

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
});

it('can list user orders with pagination', function () {
    // Membuat 15 order untuk user ini
    Order::factory()->count(15)->create(['user_id' => $this->user->id]);
    // Membuat 1 order untuk user lain (tidak boleh muncul)
    Order::factory()->create(['user_id' => User::factory()->create()->id]);

    actingAs($this->user)
        ->getJson('/api/v1/orders?per_page=10')
        ->assertOk()
        ->assertJsonCount(10, 'data') // Laravel paginator membungkus data dalam key 'data'
        ->assertJsonPath('total', 15);
});

it('can show order detail', function () {
    $order = Order::factory()->create([
        'user_id' => $this->user->id,
        'order_number' => 'ORD-123'
    ]);

    actingAs($this->user)
        ->getJson("/api/v1/orders/{$order->id}")
        ->assertOk()
        ->assertJsonPath('order_number', 'ORD-123');
});

it('prevents user from seeing others order detail', function () {
    $otherUser = User::factory()->create();
    $order = Order::factory()->create(['user_id' => $otherUser->id]);

    actingAs($this->user)
        ->getJson("/api/v1/orders/{$order->id}")
        ->assertStatus(404); // Sesuai implementasi Service yang menggunakan abort(404)
});

it('can cancel a pending order', function () {
    $order = Order::factory()->create([
        'user_id' => $this->user->id,
        'status' => 'pending'
    ]);

    actingAs($this->user)
        ->postJson("/api/v1/orders/{$order->id}/cancel", [
            'reason' => 'Salah pesan barang'
        ])
        ->assertOk()
        ->assertJsonPath('status', 'cancelled')
        ->assertJsonPath('cancel_reason', 'Salah pesan barang');

    assertDatabaseHas('orders', [
        'id' => $order->id,
        'status' => 'cancelled'
    ]);
});

it('cannot cancel a non-pending order', function () {
    $order = Order::factory()->create([
        'user_id' => $this->user->id,
        'status' => 'completed'
    ]);

    actingAs($this->user)
        ->postJson("/api/v1/orders/{$order->id}/cancel", ['reason' => 'test'])
        ->assertStatus(400);
});

it('can complete an order', function () {
    $order = Order::factory()->create([
        'user_id' => $this->user->id,
        'status' => 'paid' // Asumsi pesanan sudah dibayar
    ]);

    actingAs($this->user)
        ->postJson("/api/v1/orders/{$order->id}/complete")
        ->assertOk()
        ->assertJsonPath('status', 'completed');

    expect(Order::find($order->id)->completed_at)->not->toBeNull();
});