<?php

namespace App\Filament\Resources\InventoryAdjustments\Schemas;

use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\RepeatableEntry\TableColumn;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;

class InventoryAdjustmentDetailInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(2)
                    ->columnSpanFull()
                    ->inlineLabel()
                    ->schema([
                        TextEntry::make('adjustment_code')->label(__('global.adjustment_code')),
                        TextEntry::make('branch.name_kh')->label(__('global.branch')),
                        TextEntry::make('location.name_kh')->label(__('global.location')),
                        TextEntry::make('note')->label(__('global.remark'))->placeholder('-'),
                    ]),
                RepeatableEntry::make('inventoryAdjustmentDetails')
                    ->label(__('global.adjustment_item'))
                    ->columnSpanFull()
                    ->table([
                        TableColumn::make(__('global.no')),
                        TableColumn::make(__('global.pname_kh')),
                        TableColumn::make(__('global.mname')),
                        TableColumn::make(__('global.adjustment_type')),
                        TableColumn::make(__('global.quantity')),
                        TableColumn::make(__('global.remark')),
                    ])
                    ->schema([
                        TextEntry::make('index')
                            ->state(fn (TextEntry $component) => ((int) $component->getContainer()->getStatePath(false)) + 1),
                        TextEntry::make('product.name_kh'),
                        TextEntry::make('metric.name_kh'),
                        TextEntry::make('adjustment_type')
                            ->formatStateUsing(fn ($state) => $state == 1 ? __('global.add_stock') : __('global.deduct_stock')),
                        TextEntry::make('qty'),
                        TextEntry::make('remark')->placeholder('-'),
                    ]),
            ]);
    }
}
