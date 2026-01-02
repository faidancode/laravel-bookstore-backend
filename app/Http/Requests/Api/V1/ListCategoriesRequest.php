<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class ListCategoriesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'page' => 'integer|min:1',
            'pageSize' => 'integer|min:1|max:100',
            'sort' => 'string|in:created_at,price,title,published_at,rating',
            'order' => 'string|in:asc,desc',
            'q' => 'string|max:255',
            'authorId' => 'uuid|exists:authors,id',
            'active' => 'boolean',
            'minPrice' => 'integer|min:0',
            'maxPrice' => 'integer|min:0',
        ];
    }
}