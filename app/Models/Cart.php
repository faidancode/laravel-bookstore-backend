<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Cart extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'carts';

    /**
     * Primary key menggunakan UUID string
     */
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'user_id',
    ];

    protected $casts = [
        'id' => 'string',
        'user_id' => 'string',
    ];

    /**
     * Relasi ke user (1 cart milik 1 user)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi cart → cart_items
     */
    public function items()
    {
        return $this->hasMany(CartItem::class);
    }
}
