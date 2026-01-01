<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\ListBooksRequest;
use App\Http\Resources\Api\V1\BookResource;
use App\Services\BookService;
use Illuminate\Http\JsonResponse;

class BookController extends Controller
{
    public function __construct(
        protected BookService $bookService
    ) {}

    public function index(ListBooksRequest $request): JsonResponse
    {
        $filters = $request->validated();
        $result = $this->bookService->getAllBooks($filters);

        return response()->json([
            'ok' => true,
            'data' => BookResource::collection($result['items']),
            'meta' => $result['meta'],
            'error' => null,
        ]);
    }

    public function show(string $slug): JsonResponse
    {
        $result = $this->bookService->getBookBySlug($slug);

        if (!$result) {
            return response()->json([
                'ok' => false,
                'data' => null,
                'meta' => null,
                'error' => [
                    'message' => 'Book not found',
                    'code' => 'BOOK_NOT_FOUND',
                ],
            ], 404);
        }

        return response()->json([
            'ok' => true,
            'data' => new BookResource($result['book']),
            'meta' => [
                'averageRating' => $result['averageRating'],
                'totalReviews' => $result['totalReviews'],
            ],
            'error' => null,
        ]);
    }
}
