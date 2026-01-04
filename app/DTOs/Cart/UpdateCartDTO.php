<?php

namespace App\DTO\Cart;

class UpdateCartItemDTO
{
    public function __construct(
        public string $itemId,
        public string $userId,
        public int $quantity
    ) {}
}
