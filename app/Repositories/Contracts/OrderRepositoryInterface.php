<?php

namespace App\Repositories\Contracts;

use App\Models\Order;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface OrderRepositoryInterface
{
    public function listByUser(string $userId, int $perPage): LengthAwarePaginator;
    public function findById(string $orderId, string $userId): ?Order;
    public function createWithItems(array $orderData, array $items): Order;
    public function updateStatus(string $orderId, string $userId, string $status, array $additionalData = []): Order;
}