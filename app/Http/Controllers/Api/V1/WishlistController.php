<?php

namespace App\Http\Controllers\Api\V1;

use App\DTOs\Wishlist\CreateWishlistDTO;
use App\Http\Controllers\Controller;
use App\Services\WishlistService;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    public function __construct(
        protected WishlistService $service
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
        $dto = new CreateWishlistDTO(
            $request->user()->id,
            $request->validate([
                'items' => 'array',
                'items.*.book_id' => 'required|uuid',
            ])['items'] ?? []
        );

        return response()->json($this->service->create($dto));
    }


    public function destroyItem(Request $request, string $itemId)
    {
        return response()->json(
            $this->service->removeItem($itemId, $request->user()->id)
        );
    }
}
