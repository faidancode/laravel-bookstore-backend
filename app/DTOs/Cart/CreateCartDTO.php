<?php

namespace App\DTOs\Cart;

class CreateCartDTO
{
    public function __construct(
        public string $userId,
        /** @var array<int, array{book_id:string, quantity:int, price_cents_at_add:int}> */
        public array $items
    ) {}
}
