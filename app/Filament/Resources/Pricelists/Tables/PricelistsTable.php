<?php

namespace App\Filament\Resources\Pricelists\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\RecordActionsPosition;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class PricelistsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('pbar_code')->label(__("global.pbar_code"))
                ->state(function ($record) {
                    return $record->inventory?->pbar_code;
                })->copyable(),
                TextColumn::make('name_kh')->label(__("global.product"))->searchable()
                ->state(function ($record) {
                    return $record->inventory?->name_kh;
                }),
                TextColumn::make('metric.name')->label(__("global.mname")),
                TextColumn::make('price')->label(__("global.price_whole")),
                TextColumn::make('user_updated.name')->label(__("global.updated_by")),
                TextColumn::make('inventory.branch.name_kh')->label(__("global.branch")),
                TextColumn::make('updated_at')->label(__("global.updated_at")),
            
            ])
            ->filters([
                // TrashedFilter::make(),
            ])
            ->striped()
            ->persistFiltersInSession()
            ->searchOnBlur(true)
            ->recordActions([
                // ViewAction::make(),
                EditAction::make()
                ->hiddenLabel()
                ->tooltip("កំណត់តម្លៃ")
                ->icon(Heroicon::CurrencyDollar),
            ], position:RecordActionsPosition::BeforeCells)
            ->toolbarActions([
                // BulkActionGroup::make([
                //     DeleteBulkAction::make(),
                //     ForceDeleteBulkAction::make(),
                //     RestoreBulkAction::make(),
                // ]),
            ]);
    }
}
