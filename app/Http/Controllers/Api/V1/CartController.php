<?php

namespace App\Http\Controllers\Api\V1;

use App\DTOs\Cart\CreateCartDTO;
use App\Http\Controllers\Controller;
use App\Services\CartService;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function __construct(
        protected CartService $service
    ) {}

    public function count(Request $request)
    {
        return response()->json([
            'count' => $this->service->count($request->user()->id),
        ]);
    }

    public function show(Request $request)
    {
        return response()->json(
            $this->service->detail($request->user()->id)
        );
    }

    public function store(Request $request)
    {
        $dto = new CreateCartDTO(
            $request->user()->id,
            $request->validate([
                'items' => 'array',
                'items.*.book_id' => 'required|uuid',
                'items.*.quantity' => 'required|integer|min:1',
                'items.*.price_cents_at_add' => 'required|integer|min:0',
            ])['items'] ?? []
        );

        return response()->json($this->service->create($dto));
    }

    public function updateItem(Request $request, string $itemId)
    {
        return response()->json(
            $this->service->updateItem(
                $itemId,
                $request->user()->id,
                $request->integer('quantity')
            )
        );
    }

    public function destroyItem(Request $request, string $itemId)
    {
        return response()->json(
            $this->service->removeItem($itemId, $request->user()->id)
        );
    }

    public function decrement(Request $request, string $itemId)
    {
        return response()->json(
            $this->service->decrement($itemId, $request->user()->id)
        );
    }
}
