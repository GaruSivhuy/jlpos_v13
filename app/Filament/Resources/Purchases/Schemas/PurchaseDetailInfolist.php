<?php

namespace App\Filament\Resources\Purchases\Schemas;

use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\RepeatableEntry\TableColumn;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;

class PurchaseDetailInfolist
{
    public static function configure(Schema $schema, bool $withSummary = false): Schema
    {
        return $schema
            ->components([
                Grid::make(2)
                    ->columnSpanFull()
                    ->inlineLabel()
                    ->visible($withSummary)
                    ->schema([
                        TextEntry::make('po_code')->label(__('global.po_code')),
                        TextEntry::make('branch.name_kh')->label(__('global.branch')),
                        TextEntry::make('po_date')->label(__('global.po_date'))->date('d/m/Y'),
                        TextEntry::make('location.name_kh')->label(__('global.purchase_location')),
                        TextEntry::make('supplier.name')->label(__('global.supplier'))->placeholder('-'),
                        TextEntry::make('discount')->label(__('global.discount'))->placeholder('-'),
                        TextEntry::make('remark')->label(__('global.remark'))->placeholder('-'),
                    ]),
                RepeatableEntry::make('purchaseDetails')
                    ->label($withSummary ? __('global.purchase_item') : '')
                    ->columnSpanFull()
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
