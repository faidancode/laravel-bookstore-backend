<?php

namespace App\Repositories\Contracts;

use App\Models\Address;
use Illuminate\Support\Collection;

interface AddressRepositoryInterface
{
    public function getAllByUser(string $userId): Collection;
    public function findByIdOrFail(string $id, string $userId): Address;
    public function create(array $data): Address;
    public function update(string $id, string $userId, array $data): Address;
    public function delete(string $id, string $userId): bool;
    public function setPrimary(string $id, string $userId): bool;
}