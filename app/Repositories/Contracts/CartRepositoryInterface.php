<?php

namespace App\Repositories\Contracts;

use App\Models\Cart;

interface CartRepositoryInterface
{
    public function findByUser(string $userId): ?Cart;
    public function getCartCount(string $userId): int;
    public function getCartDetail(string $userId): ?array;
    public function createOrReplace(string $userId, array $items): array;
    public function updateItemQuantity(string $itemId, string $userId, int $qty): array;
    public function removeItem(string $itemId, string $userId): array;
    public function decrementItem(string $itemId, string $userId): array;
    public function clear(string $userId): void;
}
