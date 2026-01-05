<?php

namespace App\Repositories\Contracts;

use App\Models\Wishlist;

interface WishlistRepositoryInterface
{
    public function findByUser(string $userId): ?Wishlist;
    public function getWishlistCount(string $userId): int;
    public function getWishlistDetail(string $userId): ?array;
    public function createOrReplace(string $userId, array $items): array;
    public function removeItem(string $itemId, string $userId): array;
}
