<?php

namespace App\Services;

use App\Repositories\Contracts\AuthRepositoryInterface;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthService
{
    public function __construct(
        protected AuthRepositoryInterface $repo
    ) {}

    public function register(array $data): array
    {
        $user = $this->repo->create($data);
        $token = $user->createToken('auth_token')->plainTextToken;

        return [
            'user' => $user,
            'token' => $token,
        ];
    }

    public function login(string $email, string $password): array
    {
        $user = $this->repo->findByEmail($email);

        if (!$user || !Hash::check($password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Kredensial yang diberikan salah.'],
            ]);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return [
            'user' => $user,
            'token' => $token,
        ];
    }

    public function confirmEmail(string $token): bool
    {
        // Pastikan repo mencari user dengan verification_token yang sesuai
        $user = $this->repo->findByVerificationToken($token);

        if (!$user) {
            throw new \Exception("Token tidak valid");
        }

        return $this->repo->update($user, [
            'email_confirmed' => true,
            'email_verified_at' => now(),
            'verification_token' => null,
        ]);
    }

    public function initiateResetPassword(string $email): bool
    {
        $user = $this->repo->findByEmail($email);

        if (!$user) {
            // Tetap return true seolah-olah proses berhasil dimulai
            return false;
        }

        // Logika kirim email/generate token di sini...
        // misal: $this->repo->update($user, ['reset_token' => Str::random(64)]);

        return true;
    }

    public function resetPassword(string $email, string $newPassword): bool
    {
        $user = $this->repo->findByEmail($email);
        if (!$user) {
            throw ValidationException::withMessages(['email' => ['User tidak ditemukan.']]);
        }
        return $this->repo->update($user, ['password' => $newPassword]);
    }

    public function updateProfile(string $userId, array $data): bool
    {
        $user = $this->repo->findById($userId);
        return $this->repo->update($user, $data);
    }
}
