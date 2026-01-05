<?php

namespace App\Services;

use App\DTOs\Wishlist\CreateWishlistDTO;
use App\Repositories\Contracts\WishlistRepositoryInterface;

class WishlistService
{
    public function __construct(
        protected WishlistRepositoryInterface $repo
    ) {}

    public function count(string $userId): int
    {
        return $this->repo->getWishlistCount($userId);
    }

    public function detail(string $userId): ?array
    {
        return $this->repo->getWishlistDetail($userId);
    }

    public function create(CreateWishlistDTO $dto): array
    {
        return $this->repo->createOrReplace($dto->userId, $dto->items);
    }

    public function removeItem(string $itemId, string $userId): array
    {
        return $this->repo->removeItem($itemId, $userId);
    }
}
