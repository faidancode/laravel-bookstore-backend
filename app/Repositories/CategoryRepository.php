<?php

namespace App\Repositories;

use App\Models\Book;
use App\Models\Category;
use App\Repositories\Contracts\CategoryRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class CategoryRepository implements CategoryRepositoryInterface
{
    public function findAll(array $filters): LengthAwarePaginator
    {
        $query = Category::select('id', 'name', 'slug', 'icon')
            ->where('is_active', true);

        // Search by name
        if (!empty($filters['search'])) {
            $query->where('name', 'like', '%' . $filters['search'] . '%');
        }

        // Sorting
        $sort = $filters['sort'] ?? 'sort_order';
        $order = $filters['order'] ?? 'asc';

        match ($sort) {
            'name' => $query->orderBy('name', $order),
            default => $query->orderBy('sort_order', $order),
        };

        // Pagination
        $page = $filters['page'] ?? 1;
        $pageSize = min($filters['pageSize'] ?? 15, 100);

        return $query->paginate($pageSize, ['*'], 'page', $page);
    }

    public function findBySlug(string $slug, array $filters): ?array
    {
        // Find category
        $category = Category::where('slug', $slug)
            ->where('is_active', true)
            ->first();

        if (!$category) {
            return null;
        }

        // Build books query
        $query = Book::query()
            ->with(['author:id,name'])
            ->where('category_id', $category->id);

        // Filter: search by title
        if (!empty($filters['q'])) {
            $query->where('title', 'like', '%' . $filters['q'] . '%');
        }

        // Filter: by author
        if (!empty($filters['authorId'])) {
            $query->where('author_id', $filters['authorId']);
        }

        // Filter: active status
        if (isset($filters['active'])) {
            $query->where('is_active', (bool) $filters['active']);
        } else {
            // Default: only show active books
            $query->where('is_active', true);
        }

        // Filter: price range
        if (isset($filters['minPrice'])) {
            $query->where('price_cents', '>=', (int) $filters['minPrice']);
        }

        if (isset($filters['maxPrice'])) {
            $query->where('price_cents', '<=', (int) $filters['maxPrice']);
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
        $pageSize = min($filters['pageSize'] ?? 15, 100);

        $paginator = $query->paginate($pageSize, ['*'], 'page', $page);

        return [
            'category' => $category,
            'items' => $paginator->items(),
            'meta' => [
                'page' => $paginator->currentPage(),
                'pageSize' => $paginator->perPage(),
                'total' => $paginator->total(),
                'totalPages' => $paginator->lastPage(),
            ],
        ];
    }
}