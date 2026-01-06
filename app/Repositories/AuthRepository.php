<?php

namespace App\Repositories;

use App\Models\User;
use App\Repositories\Contracts\AuthRepositoryInterface;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthRepository implements AuthRepositoryInterface
{
    public function findByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }

    public function findById(string $id): ?User
    {
        return User::findOrFail($id);
    }

    public function create(array $data): User
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'password' => Hash::make($data['password']),
            'role' => $data['role'] ?? 'CUSTOMER',
            'verification_token' => Str::random(64)
        ]);
    }

    public function update(User $user, array $data): bool
    {
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }
        return $user->update($data);
    }

    public function findByVerificationToken(string $token): ?User
    {
        return User::where('verification_token', $token)->first();
    }
}
