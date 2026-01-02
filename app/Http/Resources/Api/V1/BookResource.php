<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Resources\Json\JsonResource;

class BookResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'isbn' => $this->isbn,
            'price' => [
                'amount' => $this->price_cents,
                'formatted' => 'Rp ' . number_format($this->price_cents / 100, 0, ',', '.'),
            ],
            'discountPrice' => $this->discount_price_cents ? [
                'amount' => $this->discount_price_cents,
                'formatted' => 'Rp ' . number_format($this->discount_price_cents / 100, 0, ',', '.'),
            ] : null,
            'stock' => $this->stock,
            'coverUrl' => $this->cover_url,
            'description' => $this->description,
            'pages' => $this->pages,
            'language' => $this->language,
            'publisher' => $this->publisher,
            'publishedAt' => $this->published_at?->format('Y-m-d'),
            'isActive' => $this->is_active,
            'ratingAvg' => $this->rating_avg,
            'category' => $this->whenLoaded('category', function () {
                return [
                    'id' => $this->category->id,
                    'name' => $this->category->name,
                    'slug' => $this->category->slug,
                ];
            }),
            'author' => $this->whenLoaded('author', function () {
                return [
                    'id' => $this->author->id,
                    'name' => $this->author->name,
                ];
            }),
            'createdAt' => $this->created_at,
            'updatedAt' => $this->updated_at,
        ];
    }
}
