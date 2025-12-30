<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Author extends Model
{
    use HasFactory, SoftDeletes;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'name',
        'slug',
        'bio',
    ];

    protected static function booted(): void
    {
        static::creating(function (Author $author) {
            if (! $author->id) {
                $author->id = (string) Str::uuid();
            }

            if (! $author->slug) {
                $author->slug = Str::slug($author->name);
            }
        });
    }
}
