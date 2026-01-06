<?php

namespace Tests\Feature;

use App\Models\Address;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use function Pest\Laravel\{actingAs, getJson, postJson, patchJson, deleteJson};

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
});

/*
|--------------------------------------------------------------------------
| Positive Scenarios
|--------------------------------------------------------------------------
*/

it('can list only current user addresses', function () {
    // Milik user yang sedang login
    Address::factory()->count(3)->create(['user_id' => $this->user->id]);
    // Milik user lain (tidak boleh muncul)
    Address::factory()->create(['user_id' => User::factory()->create()->id]);

    actingAs($this->user)
        ->getJson('/api/v1/addresses')
        ->assertOk()
        ->assertJsonCount(3);
});

it('can create a new address and automatically sets as primary if it is the first one', function () {
    $payload = [
        'label' => 'Rumah',
        'recipient_name' => 'Budi Sudarsono',
        'recipient_phone' => '08123456789',
        'street' => 'Jl. Merdeka No. 1',
        'is_primary' => false // Kita set false, tapi repo harus paksa jadi true jika pertama
    ];

    actingAs($this->user)
        ->postJson('/api/v1/addresses', $payload)
        ->assertStatus(201)
        ->assertJsonPath('is_primary', true);
});

it('can change primary address and resets the old one', function () {
    $oldPrimary = Address::factory()->create([
        'user_id' => $this->user->id, 
        'is_primary' => true
    ]);
    $newPrimaryCandidate = Address::factory()->create([
        'user_id' => $this->user->id, 
        'is_primary' => false
    ]);

    actingAs($this->user)
        ->patchJson("/api/v1/addresses/{$newPrimaryCandidate->id}/set-primary")
        ->assertOk();

    expect($newPrimaryCandidate->refresh()->is_primary)->toBeTrue();
    expect($oldPrimary->refresh()->is_primary)->toBeFalse();
});

/*
|--------------------------------------------------------------------------
| Negative Scenarios (Security & Constraints)
|--------------------------------------------------------------------------
*/

it('returns 401 when accessing addresses without login', function () {
    getJson('/api/v1/addresses')->assertStatus(401);
});

it('returns 404 when user tries to update others address', function () {
    $otherUser = User::factory()->create();
    $otherAddress = Address::factory()->create(['user_id' => $otherUser->id]);

    actingAs($this->user)
        ->patchJson("/api/v1/addresses/{$otherAddress->id}", ['label' => 'Hacker'])
        ->assertStatus(404);
});

it('returns 404 when user tries to set primary on others address', function () {
    $otherUser = User::factory()->create();
    $otherAddress = Address::factory()->create(['user_id' => $otherUser->id]);

    actingAs($this->user)
        ->patchJson("/api/v1/addresses/{$otherAddress->id}/set-primary")
        ->assertStatus(404);
});

it('automatically promotes another address to primary when current primary is deleted', function () {
    $primary = Address::factory()->create(['user_id' => $this->user->id, 'is_primary' => true]);
    $secondary = Address::factory()->create(['user_id' => $this->user->id, 'is_primary' => false]);

    actingAs($this->user)
        ->deleteJson("/api/v1/addresses/{$primary->id}")
        ->assertOk();

    expect($secondary->refresh()->is_primary)->toBeTrue();
});

it('fails validation on store if required fields are missing', function () {
    actingAs($this->user)
        ->postJson('/api/v1/addresses', [])
        ->assertStatus(422)
        ->assertJsonValidationErrors(['label', 'recipient_name', 'street']);
});