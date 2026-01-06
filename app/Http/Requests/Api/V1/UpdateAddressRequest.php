<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAddressRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; 
    }

    public function rules(): array
    {
        return [
            'label' => 'sometimes|string|max:60',
            'recipient_name' => 'sometimes|string|max:120',
            'recipient_phone' => 'sometimes|string|max:30',
            'street' => 'sometimes|string|max:255',
            'subdistrict' => 'nullable|string|max:120',
            'district' => 'nullable|string|max:120',
            'city' => 'nullable|string|max:120',
            'province' => 'nullable|string|max:120',
            'postal_code' => 'nullable|string|max:20',
            'is_primary' => 'boolean',
        ];
    }
}