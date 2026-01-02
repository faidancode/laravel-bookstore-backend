<?php

namespace App\Repositories\Contracts;

use Illuminate\Pagination\LengthAwarePaginator;

interface CategoryRepositoryInterface
{
    public function findAll(array $filters): LengthAwarePaginator;
    public function findBySlug(string $slug, array $filters): ?array;
}
