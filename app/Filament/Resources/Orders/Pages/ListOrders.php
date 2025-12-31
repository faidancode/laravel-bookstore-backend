<?php

namespace App\Filament\Resources\Orders\Pages;

use App\Filament\Resources\Orders\OrderResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListOrders extends ListRecords
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'ALL' => Tab::make(),
            'PENDING' => Tab::make()
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', "PENDING")),
            'PAID' => Tab::make()
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', "PAID")),
            'PROCESSING' => Tab::make()
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', "PROCESSING")),
            'DELIVERED' => Tab::make()
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', "DELIVERED")),
            'COMPLETED' => Tab::make()
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', "COMPLETED")),
            'CANCELLED' => Tab::make()
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', "CANCELLED")),
        ];
    }
}
