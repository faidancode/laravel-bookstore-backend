<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Book extends Model
{
    use HasFactory, SoftDeletes;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'title',
        'slug',
        'category_id',
        'author_id',
        'isbn',
        'price_cents',
        'discount_price_cents',
        'stock',
        'cover_url',
        'description',
        'pages',
        'language',
        'publisher',
        'published_at',
        'is_active',
    ];

    protected $casts = [
        'price_cents' => 'integer',
        'discount_price_cents' => 'integer',
        'stock' => 'integer',
        'published_at' => 'date',
        'is_active' => 'boolean',
        'rating_avg' => 'decimal:2',
    ];

    protected static function booted(): void
    {
        static::creating(function (Book $book) {
            if (! $book->id) {
                $book->id = (string) Str::uuid();
            }

            if (! $book->slug) {
                $book->slug = Str::slug($book->title);
            }
        });
    }

    // Relations
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function author()
    {
        return $this->belongsTo(Author::class);
    }
}
