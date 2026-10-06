<?php

namespace App\Filament\Resources\Control\ChangeProducts\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;

class ChangeProductDetailInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(2)
                    ->columnSpanFull()
                    ->inlineLabel()
                    ->schema([
                        TextEntry::make('branch.name_kh')->label(__('global.branch')),
                        TextEntry::make('change_product_type')
                            ->label(__('global.change_product_type'))
                            ->formatStateUsing(fn ($state) => ChangeProductForm::typeOptions()[$state] ?? null),
                        TextEntry::make('change_description')->label(__('global.change_description'))->placeholder('-'),
                        TextEntry::make('change_product_date')->label(__('global.change_product_date'))->date('d-m-Y'),
                        TextEntry::make('location.name_kh')->label(__('global.from_location')),
                        TextEntry::make('inventories.name_kh')->label(__('global.product')),
                        TextEntry::make('metrics.name_kh')->label(__('global.metric')),
                        TextEntry::make('qty')->label(__('global.qty')),
                        TextEntry::make('amount')->label(__('global.amount'))->numeric(),
                        TextEntry::make('total_amount')->label(__('global.total_amount'))->numeric(),
                    ]),
            ]);
    }
}
