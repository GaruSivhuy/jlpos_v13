<?php

namespace App\Filament\Resources\Pricelists\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PricelistForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                ->columnSpanFull()
                ->inlineLabel()
                ->columns(3)
                ->schema([
                    Grid::make()
                    ->columnSpan(2)
                    ->columns(1)
                    ->schema([
                        TextInput::make('branch_name')
                        ->label(__("global.branch"))
                        ->readOnly(),

                        TextInput::make('product_name')
                        ->label(__("global.product"))
                        ->readOnly(),

                        TextInput::make('metric_name')
                        ->label(__("global.cname"))
                        ->readOnly(),

                        TextInput::make('price')
                        ->label(__("global.price_whole"))
                        ->numeric(),
                    ])
                ])
            ]);
    }
}
