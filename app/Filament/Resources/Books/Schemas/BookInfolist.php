<?php

namespace App\Filament\Resources\Books\Schemas;

use App\Models\Book;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class BookInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('id')
                    ->label('ID'),
                TextEntry::make('title'),
                TextEntry::make('slug'),
                TextEntry::make('category_id'),
                TextEntry::make('author_id')
                    ->placeholder('-'),
                TextEntry::make('isbn')
                    ->placeholder('-'),
                TextEntry::make('price_cents')
                    ->numeric(),
                TextEntry::make('discount_price_cents')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('stock')
                    ->numeric(),
                TextEntry::make('cover_url'),
                TextEntry::make('description')
                    ->columnSpanFull(),
                TextEntry::make('pages')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('language')
                    ->placeholder('-'),
                TextEntry::make('publisher')
                    ->placeholder('-'),
                TextEntry::make('published_at')
                    ->date()
                    ->placeholder('-'),
                TextEntry::make('rating_avg')
                    ->numeric(),
                TextEntry::make('rating_count')
                    ->numeric(),
                IconEntry::make('is_active')
                    ->boolean(),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('deleted_at')
                    ->dateTime()
                    ->visible(fn (Book $record): bool => $record->trashed()),
            ]);
    }
}
