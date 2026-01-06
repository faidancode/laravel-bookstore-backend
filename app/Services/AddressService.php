<?php

namespace App\Services;

use App\Repositories\Contracts\AddressRepositoryInterface;

class AddressService
{
    public function __construct(
        protected AddressRepositoryInterface $repo
    ) {}

    public function list(string $userId)
    {
        return $this->repo->getAllByUser($userId);
    }

    public function store(array $data)
    {
        return $this->repo->create($data);
    }

    public function update(string $id, string $userId, array $data)
    {
        return $this->repo->update($id, $userId, $data);
    }

    public function remove(string $id, string $userId)
    {
        return $this->repo->delete($id, $userId);
    }

    public function makePrimary(string $id, string $userId)
    {
        return $this->repo->setPrimary($id, $userId);
    }
}