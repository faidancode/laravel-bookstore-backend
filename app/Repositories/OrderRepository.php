<?php

namespace App\Repositories;

use App\Models\Order;
use App\Repositories\Contracts\OrderRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class OrderRepository implements OrderRepositoryInterface
{
    public function listByUser(string $userId, int $perPage): LengthAwarePaginator
    {
        return Order::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    public function findById(string $orderId, string $userId): ?Order
    {
        return Order::where('id', $orderId)
            ->where('user_id', $userId)
            ->first();
    }

    public function updateStatus(string $orderId, string $userId, string $status, array $additionalData = []): Order
    {
        $order = $this->findById($orderId, $userId);

        if (!$order) {
            abort(404, 'Order not found');
        }

        $data = array_merge(['status' => $status], $additionalData);
        $order->update($data);

        return $order;
    }
}