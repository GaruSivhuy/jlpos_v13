<?php

namespace App\Filament\Resources\Purchases\Schemas;

use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\RepeatableEntry\TableColumn;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class PurchaseDetailInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                RepeatableEntry::make('purchaseDetails')
                    ->label('')
                    ->table([
                        TableColumn::make(__('global.no')),
                        TableColumn::make(__('global.pname_kh')),
                        TableColumn::make(__('global.mname')),
                        TableColumn::make(__('global.quantity')),
                        TableColumn::make(__('global.purchase_price')),
                        TableColumn::make(__('global.total')),
                    ])
                    ->schema([
                        TextEntry::make('index')
                            ->state(fn (TextEntry $component) => ((int) $component->getContainer()->getStatePath(false)) + 1),
                        TextEntry::make('product.name_kh'),
                        TextEntry::make('metric.name_kh'),
                        TextEntry::make('quantity'),
                        TextEntry::make('price'),
                        TextEntry::make('total')
                            ->state(fn ($record) => number_format($record->quantity * $record->price, 2)),
                    ]),
            ]);
    }
}
