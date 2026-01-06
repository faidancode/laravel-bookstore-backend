<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\OrderService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function __construct(
        protected OrderService $service
    ) {}

    public function index(Request $request)
    {
        // Mendukung pagination (per_page bisa dikirim dari frontend)
        $orders = $this->service->getUserOrders(
            $request->user()->id,
            $request->integer('per_page', 10)
        );

        return response()->json($orders);
    }

    public function show(Request $request, string $id)
    {
        return response()->json(
            $this->service->getOrderDetail($id, $request->user()->id)
        );
    }

    public function cancel(Request $request, string $id)
    {
        $request->validate([
            'reason' => 'required|string|max:255'
        ]);

        return response()->json(
            $this->service->cancelOrder(
                $id,
                $request->user()->id,
                $request->reason
            )
        );
    }

    public function complete(Request $request, string $id)
    {
        return response()->json(
            $this->service->completeOrder($id, $request->user()->id)
        );
    }

    public function checkout(Request $request)
    {
        $data = $request->validate([
            'address_id' => 'required|string|exists:addresses,id',
            'note' => 'nullable|string|max:255',
        ]);

        $order = $this->service->checkout($request->user()->id, $data);

        return response()->json([
            'message' => 'Pesanan berhasil dibuat',
            'data' => $order->load('items')
        ], 201);
    }
}
