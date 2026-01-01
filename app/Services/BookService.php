<?php

namespace App\Services;

use App\Repositories\Contracts\BookRepositoryInterface;

class BookService
{
    public function __construct(
        protected BookRepositoryInterface $bookRepository
    ) {}

    public function getAllBooks(array $filters): array
    {
        $paginator = $this->bookRepository->findAll($filters);

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

    public function getBookBySlug(string $slug): ?array
    {
        return $this->bookRepository->findBySlug($slug);
    }
}