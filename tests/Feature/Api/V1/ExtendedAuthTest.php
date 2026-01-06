<?php

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Foundation\Testing\RefreshDatabase;
use function Pest\Laravel\{actingAs, postJson, getJson, patchJson};

uses(RefreshDatabase::class);

/**
 * Test Forgot Password & Enumeration Protection
 */
it('gives ambiguous response on forgot password to prevent user enumeration', function () {
    // Skenario 1: Email tidak terdaftar
    $postData = ['email' => 'unknown@example.com'];
    
    postJson('/api/v1/auth/forgot-password', $postData)
        ->assertOk()
        ->assertJson([
            'success' => true,
            'message' => 'If your email is in our system, you will receive a link',
            'emailSent' => false
        ]);

    // Skenario 2: Email terdaftar
    $user = User::factory()->create(['email' => 'registered@example.com']);
    $postData = ['email' => 'registered@example.com'];

    postJson('/api/v1/auth/forgot-password', $postData)
        ->assertOk()
        ->assertJson([
            'success' => true,
            'message' => 'If your email is in our system, you will receive a link',
            'emailSent' => true
        ]);
});

/**
 * Test Confirm Email (Unauthenticated)
 */
it('can confirm email with a valid token without being logged in', function () {
    $token = Str::random(32);
    $user = User::factory()->create([
        'email_confirmed' => false,
        'verification_token' => $token
    ]);

    getJson("/api/v1/auth/confirm-email?token={$token}")
        ->assertOk()
        ->assertJson([
            'success' => true,
            'verified' => true,
            'message' => 'Email verified successfully'
        ]);

    expect($user->fresh()->email_confirmed)->toBeTrue();
    expect($user->fresh()->verification_token)->toBeNull();
});

it('returns verified false for invalid verification token', function () {
    getJson("/api/v1/auth/confirm-email?token=invalid-token")
        ->assertOk() // Tetap 200 sesuai logika controller Anda
        ->assertJson([
            'success' => true,
            'verified' => false,
            'message' => 'Invalid or expired token'
        ]);
});

/**
 * Test Reset Password
 */
it('can reset password using valid email and token', function () {
    $user = User::factory()->create([
        'email' => 'user@example.com',
        'password' => bcrypt('OldPassword123')
    ]);
    
    // Asumsikan token divalidasi di Service
    $postData = [
        'email' => 'user@example.com',
        'token' => 'valid-reset-token',
        'password' => 'NewSecurePassword123',
        'password_confirmation' => 'NewSecurePassword123'
    ];

    postJson('/api/v1/auth/reset-password', $postData)
        ->assertOk()
        ->assertJson(['message' => 'Password berhasil diperbarui.']);
});

/**
 * Test Update Profile (Authenticated)
 */
it('can update profile information when authenticated', function () {
    $user = User::factory()->create([
        'name' => 'Old Name',
        'phone' => '111111'
    ]);

    $patchData = [
        'name' => 'New Name',
        'phone' => '08123456789'
    ];

    actingAs($user)
        ->patchJson('/api/v1/auth/profile', $patchData)
        ->assertOk()
        ->assertJson(['message' => 'Profil berhasil diperbarui.']);

    $user->refresh();
    expect($user->name)->toBe('New Name');
    expect($user->phone)->toBe('08123456789');
});

it('forbids profile update without authentication', function () {
    patchJson('/api/v1/auth/profile', ['name' => 'Should Fail'])
        ->assertStatus(401);
});