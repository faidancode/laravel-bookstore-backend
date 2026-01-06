<?php

namespace App\Services;

use App\Repositories\Contracts\OrderRepositoryInterface;
use Carbon\Carbon;

class OrderService
{
    public function __construct(
        protected OrderRepositoryInterface $repo
    ) {}

    public function getUserOrders(string $userId, int $perPage = 10)
    {
        return $this->repo->listByUser($userId, $perPage);
    }

    public function getOrderDetail(string $orderId, string $userId)
    {
        $order = $this->repo->findById($orderId, $userId);
        if (!$order) abort(404, 'Order tidak ditemukan');
        return $order;
    }

    public function cancelOrder(string $orderId, string $userId, string $reason)
    {
        $order = $this->repo->findById($orderId, $userId);

        if ($order->status !== 'pending') {
            abort(400, 'Hanya pesanan pending yang dapat dibatalkan');
        }

        return $this->repo->updateStatus($orderId, $userId, 'cancelled', [
            'cancelled_at' => Carbon::now(),
            'cancel_reason' => $reason
        ]);
    }

    public function completeOrder(string $orderId, string $userId)
    {
        $order = $this->repo->findById($orderId, $userId);

        // Contoh: Hanya bisa complete jika status sudah 'shipped' atau 'paid'
        // Tergantung workflow bisnis Anda
        if ($order->status === 'cancelled' || $order->status === 'completed') {
            abort(400, 'Status pesanan tidak valid untuk diselesaikan');
        }

        return $this->repo->updateStatus($orderId, $userId, 'completed', [
            'completed_at' => Carbon::now()
        ]);
    }
}