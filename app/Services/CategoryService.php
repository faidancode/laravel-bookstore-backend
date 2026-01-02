<?php

namespace App\Services;

use App\Repositories\Contracts\CategoryRepositoryInterface;

class CategoryService
{
    public function __construct(
        protected CategoryRepositoryInterface $categoryRepository
    ) {}

    public function getAllCategorys(array $filters): array
    {
        $paginator = $this->categoryRepository->findAll($filters);

        return [
            'items' => $paginator->items(),
            'meta' => [
                'page' => $paginator->currentPage(),
                'pageSize' => $paginator->perPage(),
                'total' => $paginator->total(),
                'totalPages' => $paginator->lastPage(),
            ],
        ];
    }

    public function getCategoryWithBooks(string $slug, array $filters): ?array
    {
        return $this->categoryRepository->findBySlug($slug, $filters);
    }
}