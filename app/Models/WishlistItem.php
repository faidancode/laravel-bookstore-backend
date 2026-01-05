<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class WishlistItem extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'wishlist_items';

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'wishlist_id',
        'book_id',
    ];

    public function wishlist()
    {
        return $this->belongsTo(Wishlist::class);
    }

    public function book()
    {
        return $this->belongsTo(Book::class);
    }
}
