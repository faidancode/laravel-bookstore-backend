<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use function Pest\Laravel\{postJson, assertDatabaseHas, withHeader};

uses(RefreshDatabase::class);

it('can register a new user', function () {
    $registrationData = [
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'phone' => '08123456789',
        'password' => 'Password123', // Memenuhi syarat letters, numbers, mixedCase
        'password_confirmation' => 'Password123',
    ];

    $response = postJson('/api/v1/auth/register', $registrationData);

    $response->assertStatus(201)
        ->assertJsonStructure([
            'message',
            'data' => ['id', 'name', 'email'],
            'access_token'
        ]);

    assertDatabaseHas('users', [
        'email' => 'john@example.com',
        'name' => 'John Doe'
    ]);
});

it('can login with valid credentials', function () {
    $user = User::factory()->create([
        'email' => 'test@example.com',
        'password' => bcrypt('password123'),
    ]);

    $loginData = [
        'email' => 'test@example.com',
        'password' => 'password123',
    ];

    $response = postJson('/api/v1/auth/login', $loginData);

    $response->assertStatus(200)
        ->assertJsonStructure(['access_token', 'token_type']);
});

it('cannot login with invalid password', function () {
    $user = User::factory()->create([
        'email' => 'test@example.com',
        'password' => bcrypt('password123'),
    ]);

    $loginData = [
        'email' => 'test@example.com',
        'password' => 'wrong-password',
    ];

    $response = postJson('/api/v1/auth/login', $loginData);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['email']);
});

it('can logout', function () {
    $user = User::factory()->create();
    $token = $user->createToken('test-token')->plainTextToken;

    withHeader('Authorization', 'Bearer ' . $token)
        ->postJson('/api/v1/auth/logout')
        ->assertOk();

    expect($user->tokens()->count())->toBe(0);
});
