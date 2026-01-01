<?php

namespace App\Repositories;

use App\Models\Book;
use App\Repositories\Contracts\BookRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class BookRepository implements BookRepositoryInterface
{
    public function findAll(array $filters): LengthAwarePaginator
    {
        $query = Book::query()
            ->with(['category:id,name,slug', 'author:id,name'])
            ->select('books.*');

        // Filter by category slug
        if (!empty($filters['category'])) {
            $query->whereHas('category', function ($q) use ($filters) {
                $q->where('slug', $filters['category']);
            });
        }

        // Filter by category ID
        if (!empty($filters['categoryId'])) {
            $query->where('category_id', $filters['categoryId']);
        }

        // Filter by author ID
        if (!empty($filters['authorId'])) {
            $query->where('author_id', $filters['authorId']);
        }

        // Search by title or ISBN
        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('title', 'like', '%' . $filters['search'] . '%')
                    ->orWhere('isbn', 'like', '%' . $filters['search'] . '%');
            });
        }

        // Filter by price range
        if (isset($filters['minPrice']) && isset($filters['maxPrice'])) {
            $minPrice = (int) $filters['minPrice'];
            $maxPrice = (int) $filters['maxPrice'];
            
            // Swap if minPrice > maxPrice
            if ($minPrice > $maxPrice) {
                [$minPrice, $maxPrice] = [$maxPrice, $minPrice];
            }

            $query->whereBetween('price_cents', [$minPrice, $maxPrice]);
        } elseif (isset($filters['minPrice'])) {
            $query->where('price_cents', '>=', (int) $filters['minPrice']);
        } elseif (isset($filters['maxPrice'])) {
            $query->where('price_cents', '<=', (int) $filters['maxPrice']);
        }

        // Filter by active status
        if (!isset($filters['includeInactive']) || !$filters['includeInactive']) {
            $query->where('is_active', true);
        }

        // Sorting
        $sort = $filters['sort'] ?? 'created_at';
        $order = $filters['order'] ?? 'desc';
        
        match ($sort) {
            'price' => $query->orderBy('price_cents', $order),
            'title' => $query->orderBy('title', $order),
            'published_at' => $query->orderBy('published_at', $order),
            'rating' => $query->orderBy('rating_avg', $order),
            default => $query->orderBy('created_at', $order),
        };

        // Pagination
        $page = $filters['page'] ?? 1;
        $pageSize = min($filters['pageSize'] ?? 15, 100); // Max 100 items

        return $query->paginate($pageSize, ['*'], 'page', $page);
    }

    public function findBySlug(string $slug): ?array
    {
        $book = Book::with(['category:id,name,slug', 'author:id,name'])
            ->where('slug', $slug)
            ->where('is_active', true)
            ->first();

        if (!$book) {
            return null;
        }

        // You can extend this to fetch reviews if you have Review model
        return [
            'book' => $book,
            'averageRating' => $book->rating_avg ?? 0,
            'totalReviews' => 0, // Replace with actual review count
        ];
    }
}