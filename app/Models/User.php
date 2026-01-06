<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;

use function Illuminate\Log\log;

class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable, HasUuids, SoftDeletes;
    /**
     * Primary key is UUID (string)
     */
    public $incrementing = false;
    protected $keyType = 'string';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'id',
        'name',
        'email',
        'phone',
        'password',
        'is_active',
        'email_confirmed',
        'verification_token',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Attribute casting.
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
        'email_confirmed' => 'boolean',
    ];

    public function scopeCustomer($query)
    {
        return $query->where('role', 'CUSTOMER');
    }

    /**
     * Auto-generate UUID when creating user
     */
    protected static function booted(): void
    {
        static::creating(function (User $user) {
            if (! $user->getKey()) {
                $user->{$user->getKeyName()} = (string) Str::uuid();
            }
        });
    }

    public function canAccessPanel(Panel $panel): bool
    {
        // Untuk testing: return true agar SEMUA user bisa masuk.
        // Untuk produksi: batasi berdasarkan email atau role.
        log('test login');
        return true;
    }
}
