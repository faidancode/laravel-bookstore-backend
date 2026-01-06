<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\CreateAddressRequest;
use App\Http\Requests\Api\V1\UpdateAddressRequest;
use App\Services\AddressService;
use Illuminate\Http\Request;

class AddressController extends Controller
{
    public function __construct(protected AddressService $service) {}

    public function index(Request $request)
    {
        return response()->json($this->service->list($request->user()->id));
    }

    public function store(CreateAddressRequest $request)
    {
        $data = $request->validate([
            'label' => 'required|string|max:60',
            'recipient_name' => 'required|string|max:120',
            'recipient_phone' => 'required|string|max:30',
            'street' => 'required|string|max:255',
            'subdistrict' => 'nullable|string|max:120',
            'district' => 'nullable|string|max:120',
            'city' => 'nullable|string|max:120',
            'province' => 'nullable|string|max:120',
            'postal_code' => 'nullable|string|max:20',
            'is_primary' => 'boolean',
        ]);

        $data['user_id'] = $request->user()->id;
        return response()->json($this->service->store($data), 201);
    }

    public function update(UpdateAddressRequest $request, string $id)
    {
        $data = $request->validate([
            'label' => 'string|max:60',
            'recipient_name' => 'string|max:120',
            'is_primary' => 'boolean',
        ]);

        return response()->json($this->service->update($id, $request->user()->id, $data));
    }

    public function destroy(Request $request, string $id)
    {
        $this->service->remove($id, $request->user()->id);
        return response()->json(['message' => 'Address deleted']);
    }

    public function setPrimary(Request $request, string $id)
    {
        $this->service->makePrimary($id, $request->user()->id);
        return response()->json(['message' => 'Primary address updated']);
    }
}