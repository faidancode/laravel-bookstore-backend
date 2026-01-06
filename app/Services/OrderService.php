<?php

namespace App\Services;

use App\Repositories\Contracts\AddressRepositoryInterface;
use App\Repositories\Contracts\CartRepositoryInterface;
use App\Repositories\Contracts\OrderRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderService
{
    public function __construct(
        protected OrderRepositoryInterface $orderRepo,
        protected AddressRepositoryInterface $addressRepo,
        protected CartRepositoryInterface $cartRepo
    ) {}

    public function checkout(string $userId, array $data)
    {
        return DB::transaction(function () use ($userId, $data) {
            // 1. Validasi Cart
            $cart = $this->cartRepo->findByUser($userId);
            if (!$cart || $cart->items->isEmpty()) {
                abort(400, 'Keranjang belanja Anda kosong.');
            }



            // 2. Validasi & Snapshot Alamat
            $address = $this->addressRepo->findByIdOrFail($data['address_id'], $userId);

            // 3. Siapkan Data Item & Hitung Total
            $orderItems = [];
            $subtotal = 0;

            foreach ($cart->items as $cartItem) {
                $itemTotal = $cartItem->quantity * $cartItem->book->price_cents;
                $subtotal += $itemTotal;

                $book = $cartItem->book;
                if ($book->stock < $cartItem->quantity) {
                    // Berikan pesan yang spesifik agar user tahu buku mana yang bermasalah
                    abort(400, "Stok buku '{$book->title}' tidak mencukupi.");
                }

                $orderItems[] = [
                    'book_id' => $cartItem->book_id,
                    'title_snapshot' => $cartItem->book->title,
                    'unit_price_cents' => $cartItem->book->price_cents,
                    'quantity' => $cartItem->quantity,
                    'total_cents' => $itemTotal,
                ];

                // --- PENGURANGAN STOK ---
                $book->decrement('stock', $cartItem->quantity);
            }

            $shipping = 15000; // Contoh statis ongkir
            $total = $subtotal + $shipping;

            // 4. Buat Order
            $orderId = Str::uuid()->toString();
            $order = $this->orderRepo->createWithItems([
                'id' => $orderId,
                'midtrans_order_id' => $orderId,
                'order_number' => 'ORD-' . strtoupper(Str::random(10)),
                'user_id' => $userId,
                'status' => 'pending',
                'address_snapshot' => $address->toArray(),
                'subtotal_cents' => $subtotal,
                'shipping_cents' => $shipping,
                'total_cents' => $total,
                'placed_at' => now(),
            ], $orderItems);

            // 5. Kosongkan Cart setelah sukses checkout
            $this->cartRepo->clear($userId);

            return $order;
        });
    }

    public function getUserOrders(string $userId, int $perPage = 10)
    {
        return $this->orderRepo->listByUser($userId, $perPage);
    }

    public function getOrderDetail(string $orderId, string $userId)
    {
        $order = $this->orderRepo->findById($orderId, $userId);
        if (!$order) abort(404, 'Order tidak ditemukan');
        return $order;
    }

    public function cancelOrder(string $orderId, string $userId, string $reason)
    {
        $order = $this->orderRepo->findById($orderId, $userId);

        if ($order->status !== 'pending') {
            abort(400, 'Hanya pesanan pending yang dapat dibatalkan');
        }

        return $this->orderRepo->updateStatus($orderId, $userId, 'cancelled', [
            'cancelled_at' => Carbon::now(),
            'cancel_reason' => $reason
        ]);
    }

    public function completeOrder(string $orderId, string $userId)
    {
        $order = $this->orderRepo->findById($orderId, $userId);

        // Contoh: Hanya bisa complete jika status sudah 'shipped' atau 'paid'
        // Tergantung workflow bisnis Anda
        if ($order->status === 'cancelled' || $order->status === 'completed') {
            abort(400, 'Status pesanan tidak valid untuk diselesaikan');
        }

        return $this->orderRepo->updateStatus($orderId, $userId, 'completed', [
            'completed_at' => Carbon::now()
        ]);
    }
}
