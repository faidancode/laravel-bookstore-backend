<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class CreateAddressRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Autentikasi sudah ditangani middleware auth:sanctum
    }

    public function rules(): array
    {
        return [
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
        ];
    }
}