<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\ListCategoriesRequest;
use App\Http\Resources\Api\V1\BookResource;
use App\Http\Resources\Api\V1\CategoryResource;
use App\Services\CategoryService;
use Illuminate\Http\JsonResponse;

class CategoryController extends Controller
{
    public function __construct(
        protected CategoryService $categoryService
    ) {}

    public function index(ListCategoriesRequest $request): JsonResponse
    {
        $filters = $request->validated();
        $result = $this->categoryService->getAllCategorys($filters);

        return response()->json([
            'ok' => true,
            'data' => CategoryResource::collection($result['items']),
            'meta' => $result['meta'],
            'error' => null,
        ]);
    }

    public function showWithBooks(string $slug, ListCategoriesRequest $request): JsonResponse
    {
        $filters = $request->validated();
        $result = $this->categoryService->getCategoryWithBooks($slug, $filters);

        if (!$result) {
            return response()->json([
                'ok' => false,
                'data' => null,
                'meta' => null,
                'error' => [
                    'message' => 'Category not found',
                    'code' => 'CATEGORY_NOT_FOUND',
                ],
            ], 404);
        }

        return response()->json([
            'ok' => true,
            'data' => [
                'category' => new CategoryResource($result['category']),
                'books' => BookResource::collection($result['items']),
            ],
            'meta' => $result['meta'],
            'error' => null,
        ]);
    }
}
