<?php

namespace App\DTOs\Wishlist;

class CreateWishlistDTO
{
    public function __construct(
        public string $userId,
        /** @var array<int, array{book_id:string}> */
        public array $items
    ) {}
}
