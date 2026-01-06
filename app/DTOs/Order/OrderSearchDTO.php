<?php

namespace App\DTOs\Order;

class OrderSearchDTO
{
    public function __construct(
        public string $userId,
        public int $perPage = 10
    ) {}
}