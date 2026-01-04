<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class CartItem extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'cart_items';

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'cart_id',
        'book_id',
        'quantity',
        'price_cents_at_add',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'price_cents_at_add' => 'integer',
    ];

    public function cart()
    {
        return $this->belongsTo(Cart::class);
    }

    public function book()
    {
        return $this->belongsTo(Book::class);
    }
}
