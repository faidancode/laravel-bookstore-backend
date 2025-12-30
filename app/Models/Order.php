<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use SoftDeletes;

    protected $table = 'orders';
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false;

    const DELETED_AT = 'deleted_at';

    protected $fillable = [
        'id',
        'order_number',
        'user_id',
        'status',
        'payment_method',
        'payment_status',
        'address_snapshot',
        'subtotal_cents',
        'discount_cents',
        'shipping_cents',
        'total_cents',
        'note',
        'placed_at',
        'paid_at',
        'cancelled_at',
        'cancel_reason',
        'completed_at',
        'receipt_no',
        'midtrans_order_id',
        'snap_token',
        'snap_redirect_url',
        'snap_token_expired_at',
    ];

    protected $casts = [
        'address_snapshot' => 'array',
        'placed_at' => 'datetime',
        'paid_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'completed_at' => 'datetime',
        'snap_token_expired_at' => 'datetime',
    ];
}
