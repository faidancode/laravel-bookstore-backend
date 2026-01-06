<?php

namespace App\Repositories;

use App\Models\Address;
use App\Repositories\Contracts\AddressRepositoryInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class AddressRepository implements AddressRepositoryInterface
{
    public function getAllByUser(string $userId): Collection
    {
        return Address::where('user_id', $userId)
            ->orderBy('is_primary', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Skenario Negatif: Ownership Check & Resource Not Found
     */
    public function findByIdOrFail(string $id, string $userId): Address
    {
        $address = Address::where('id', $id)
            ->where('user_id', $userId)
            ->first();

        if (!$address) {
            throw (new ModelNotFoundException())->setModel(Address::class, [$id]);
        }

        return $address;
    }

    /**
     * Skenario Negatif: Data Integrity dengan Transaction
     */
    public function create(array $data): Address
    {
        return DB::transaction(function () use ($data) {
            if ($data['is_primary'] ?? false) {
                $this->resetPrimaryFlag($data['user_id']);
            }

            // Jika ini alamat pertama user, paksa jadi primary
            $count = Address::where('user_id', $data['user_id'])->count();
            if ($count === 0) {
                $data['is_primary'] = true;
            }

            return Address::create($data);
        });
    }

    public function update(string $id, string $userId, array $data): Address
    {
        return DB::transaction(function () use ($id, $userId, $data) {
            $address = $this->findByIdOrFail($id, $userId);

            // Skenario Negatif: Jika ingin mengubah ke primary, reset yang lain
            if (isset($data['is_primary']) && $data['is_primary'] === true) {
                $this->resetPrimaryFlag($userId);
            }

            // Skenario Negatif: Mencegah user menghilangkan flag primary jika hanya punya 1 alamat
            if (isset($data['is_primary']) && $data['is_primary'] === false && $address->is_primary) {
                // Opsional: Tetap biarkan true jika ini satu-satunya alamat
                $data['is_primary'] = true;
            }

            $address->update($data);
            return $address;
        });
    }

    /**
     * Skenario Negatif: Dependency Constraint & State Validation
     */
    public function delete(string $id, string $userId): bool
    {
        return DB::transaction(function () use ($id, $userId) {
            $address = $this->findByIdOrFail($id, $userId);

            // Skenario: Jika alamat utama dihapus, pindahkan primary ke alamat lain yang tersisa
            if ($address->is_primary) {
                $nextAddress = Address::where('user_id', $userId)
                    ->where('id', '!=', $id)
                    ->first();

                if ($nextAddress) {
                    $nextAddress->update(['is_primary' => true]);
                }
            }

            return $address->delete();
        });
    }

    public function setPrimary(string $id, string $userId): bool
    {
        return DB::transaction(function () use ($id, $userId) {
            // 1. Cek kepemilikan: Jika bukan miliknya, findByIdOrFail akan melempar 404
            $address = $this->findByIdOrFail($id, $userId);

            // 2. Skenario Negatif: Jika alamat tersebut sudah primary, tidak perlu proses ulang
            if ($address->is_primary) {
                return true;
            }

            // 3. Reset semua alamat lain milik user ini menjadi is_primary = false
            $this->resetPrimaryFlag($userId);

            // 4. Set alamat terpilih menjadi primary
            return $address->update(['is_primary' => true]);
        });
    }

    private function resetPrimaryFlag(string $userId): void
    {
        Address::where('user_id', $userId)
            ->where('is_primary', true)
            ->update(['is_primary' => false]);
    }
}
