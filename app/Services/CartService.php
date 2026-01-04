<?php

namespace App\Services;

use App\DTOs\Cart\CreateCartDTO;
use App\Repositories\Contracts\CartRepositoryInterface;

class CartService
{
    public function __construct(
        protected CartRepositoryInterface $repo
    ) {}

    public function count(string $userId): int
    {
        return $this->repo->getCartCount($userId);
    }

    public function detail(string $userId): ?array
    {
        return $this->repo->getCartDetail($userId);
    }

    public function create(CreateCartDTO $dto): array
    {
        return $this->repo->createOrReplace($dto->userId, $dto->items);
    }

    public function updateItem(string $itemId, string $userId, int $qty): array
    {
        return $this->repo->updateItemQuantity($itemId, $userId, $qty);
    }

    public function removeItem(string $itemId, string $userId): array
    {
        return $this->repo->removeItem($itemId, $userId);
    }

    public function decrement(string $itemId, string $userId): array
    {
        return $this->repo->decrementItem($itemId, $userId);
    }
}
