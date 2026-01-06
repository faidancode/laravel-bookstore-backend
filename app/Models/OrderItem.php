<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class OrderItem extends Model
{
    use HasUuids;

    protected $table = 'order_items';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'order_id',
        'book_id',
        'title_snapshot',
        'unit_price_cents',
        'quantity',
        'total_cents',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}