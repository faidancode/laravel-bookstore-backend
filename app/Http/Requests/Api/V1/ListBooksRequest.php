<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class ListBooksRequest extends FormRequest
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
            'category' => 'string',
            'categoryId' => 'uuid|exists:categories,id',
            'authorId' => 'uuid|exists:authors,id',
            'search' => 'string|max:255',
            'minPrice' => 'integer|min:0',
            'maxPrice' => 'integer|min:0',
            'includeInactive' => 'boolean',
        ];
    }
}
