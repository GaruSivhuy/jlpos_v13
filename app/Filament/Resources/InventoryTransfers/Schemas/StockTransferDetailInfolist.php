<?php

namespace App\Filament\Resources\InventoryTransfers\Schemas;

use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\RepeatableEntry\TableColumn;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;

class StockTransferDetailInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(2)
                    ->columnSpanFull()
                    ->inlineLabel()
                    ->schema([
                        TextEntry::make('st_code')->label(__('global.st_code')),
                        TextEntry::make('branch.name_kh')->label(__('global.branch')),
                        TextEntry::make('fromLocation.name_kh')->label(__('global.from_location')),
                        TextEntry::make('toLocation.name_kh')->label(__('global.to_location')),
                    ]),
                RepeatableEntry::make('stockTransferDetails')
                    ->label(__('global.transfer_item'))
                    ->columnSpanFull()
                    ->table([
                        TableColumn::make(__('global.no')),
                        TableColumn::make(__('global.pname_kh')),
                        TableColumn::make(__('global.mname')),
                        TableColumn::make(__('global.transfer_quantity')),
                        TableColumn::make(__('global.remark')),
                    ])
                    ->schema([
                        TextEntry::make('index')
                            ->state(fn (TextEntry $component) => ((int) $component->getContainer()->getStatePath(false)) + 1),
                        TextEntry::make('product.name_kh'),
                        TextEntry::make('metric.name_kh'),
                        TextEntry::make('qty'),
                        TextEntry::make('remark')->placeholder('-'),
                    ]),
            ]);
    }
}
