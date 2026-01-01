<?php

namespace App\Repositories\Contracts;

use Illuminate\Pagination\LengthAwarePaginator;

interface BookRepositoryInterface
{
    public function findAll(array $filters): LengthAwarePaginator;
    public function findBySlug(string $slug): ?array;
}
