<?php

namespace App\Repositories;

use App\Models\Cart;
use App\Models\CartItem;
use App\Repositories\Contracts\CartRepositoryInterface;
use Illuminate\Support\Facades\DB;

class CartRepository implements CartRepositoryInterface
{
    public function findByUser(string $userId): ?Cart
    {
        return Cart::where('user_id', $userId)->first();
    }

    public function getCartCount(string $userId): int
    {
        $cart = $this->findByUser($userId);
        return $cart ? $cart->items()->count() : 0;
    }

    public function getCartDetail(string $userId): ?array
    {
        $cart = Cart::where('user_id', $userId)->first();
        if (! $cart) return null;

        $items = CartItem::query()
            ->where('cart_id', $cart->id)
            ->join('books', 'books.id', '=', 'cart_items.book_id')
            ->leftJoin('authors', 'authors.id', '=', 'books.author_id')
            ->orderBy('cart_items.created_at')
            ->get([
                'cart_items.*',
                'books.title as book_title',
                'books.cover_url',
                'authors.name as author_name',
                'books.category_id',
            ]);

        return [
            'id' => $cart->id,
            'user_id' => $cart->user_id,
            'updated_at' => $cart->updated_at,
            'items' => $items,
        ];
    }

    public function createOrReplace(string $userId, array $items): array
    {
        return DB::transaction(function () use ($userId, $items) {
            $cart = Cart::firstOrCreate(
                ['user_id' => $userId],
            );

            CartItem::where('cart_id', $cart->id)->delete();

            foreach ($items as $item) {
                CartItem::create([
                    'cart_id' => $cart->id,
                    'book_id' => $item['book_id'],
                    'quantity' => $item['quantity'],
                    'price_cents_at_add' => $item['price_cents_at_add'],
                ]);
            }

            $cart->touch();

            return $this->getCartDetail($userId);
        });
    }

    public function updateItemQuantity(string $itemId, string $userId, int $qty): array
    {
        if ($qty < 1) {
            abort(400, 'Quantity must be at least 1');
        }

        $item = CartItem::with('cart', 'book')->findOrFail($itemId);

        if ($item->cart->user_id !== $userId) {
            abort(403, 'Forbidden');
        }

        if ($item->book && $qty > $item->book->stock) {
            abort(400, 'Quantity exceeds stock');
        }

        $item->update(['quantity' => $qty]);
        $item->cart->touch();

        return $this->getCartDetail($userId);
    }

    public function removeItem(string $itemId, string $userId): array
    {
        $item = CartItem::with('cart')->findOrFail($itemId);

        if ($item->cart->user_id !== $userId) {
            abort(403, 'Forbidden');
        }

        $item->delete();
        $item->cart->touch();

        return $this->getCartDetail($userId);
    }

    public function decrementItem(string $itemId, string $userId): array
    {
        $item = CartItem::with('cart')->findOrFail($itemId);

        if ($item->cart->user_id !== $userId) {
            abort(403, 'Forbidden');
        }

        if ($item->quantity > 1) {
            $item->decrement('quantity');
        } else {
            $item->delete();
        }

        $item->cart->touch();

        return $this->getCartDetail($userId);
    }
}
