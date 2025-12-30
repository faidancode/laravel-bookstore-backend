<?php

namespace App\Filament\Resources\Books\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class BookForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->required(),
                TextInput::make('slug')
                    ->required(),
                TextInput::make('category_id')
                    ->required(),
                TextInput::make('author_id')
                    ->default(null),
                TextInput::make('isbn')
                    ->default(null),
                TextInput::make('price_cents')
                    ->required()
                    ->numeric(),
                TextInput::make('discount_price_cents')
                    ->numeric()
                    ->default(null),
                TextInput::make('stock')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('cover_url')
                    ->url()
                    ->required(),
                Textarea::make('description')
                    ->required()
                    ->columnSpanFull(),
                TextInput::make('pages')
                    ->numeric()
                    ->default(null),
                TextInput::make('language')
                    ->default(null),
                TextInput::make('publisher')
                    ->default(null),
                DatePicker::make('published_at'),
                TextInput::make('rating_avg')
                    ->required()
                    ->numeric()
                    ->default(0.0),
                TextInput::make('rating_count')
                    ->required()
                    ->numeric()
                    ->default(0),
                Toggle::make('is_active')
                    ->required(),
            ]);
    }
}
