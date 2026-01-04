<?php

use App\Models\Book;

function createBook(array $overrides = []): Book
{
    return Book::factory()
        ->for(test()->category, 'category')
        ->for(test()->author, 'author')
        ->state(['is_active' => true])
        ->create($overrides);
}
