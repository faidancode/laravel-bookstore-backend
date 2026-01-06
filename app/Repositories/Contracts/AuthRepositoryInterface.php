<?php

namespace App\Repositories\Contracts;

use App\Models\User;

interface AuthRepositoryInterface
{
    public function findByEmail(string $email): ?User;
    public function findById(string $id): ?User;
    public function findByVerificationToken(string $token): ?User; // Tambahan baru
    public function create(array $data): User;
    public function update(User $user, array $data): bool;
}
