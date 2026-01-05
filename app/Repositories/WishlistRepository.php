<?php

namespace App\Repositories;

use App\Models\Wishlist;
use App\Models\WishlistItem;
use App\Repositories\Contracts\WishlistRepositoryInterface;
use Illuminate\Support\Facades\DB;

class WishlistRepository implements WishlistRepositoryInterface
{
    public function findByUser(string $userId): ?Wishlist
    {
        return Wishlist::where('user_id', $userId)->first();
    }

    public function getWishlistCount(string $userId): int
    {
        $wishlist = $this->findByUser($userId);
        return $wishlist ? $wishlist->items()->count() : 0;
    }

    public function getWishlistDetail(string $userId): ?array
    {
        $wishlist = Wishlist::where('user_id', $userId)->first();
        if (! $wishlist) return null;

        $items = WishlistItem::query()
            ->where('wishlist_id', $wishlist->id)
            ->join('books', 'books.id', '=', 'wishlist_items.book_id')
            ->leftJoin('authors', 'authors.id', '=', 'books.author_id')
            ->orderBy('wishlist_items.created_at')
            ->get([
                'wishlist_items.*',
                'books.title as book_title',
                'books.cover_url',
                'authors.name as author_name',
                'books.category_id',
            ]);

        return [
            'id' => $wishlist->id,
            'user_id' => $wishlist->user_id,
            'updated_at' => $wishlist->updated_at,
            'items' => $items,
        ];
    }

    public function createOrReplace(string $userId, array $items): array
    {
        return DB::transaction(function () use ($userId, $items) {
            $wishlist = Wishlist::firstOrCreate(
                ['user_id' => $userId],
            );

            WishlistItem::where('wishlist_id', $wishlist->id)->delete();

            foreach ($items as $item) {
                WishlistItem::create([
                    'wishlist_id' => $wishlist->id,
                    'book_id' => $item['book_id'],
                ]);
            }

            $wishlist->touch();

            return $this->getWishlistDetail($userId);
        });
    }

    public function removeItem(string $itemId, string $userId): array
    {
        $item = WishlistItem::with('wishlist')->findOrFail($itemId);

        if ($item->wishlist->user_id !== $userId) {
            abort(403, 'Forbidden');
        }

        $item->delete();
        $item->wishlist->touch();

        return $this->getWishlistDetail($userId);
    }

}
