<?php

namespace App\Filament\Resources\Suppliers\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\RecordActionsPosition;
use Filament\Tables\Table;

class SuppliersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->label(__('global.id'))->state(fn ($record) => $record ? 'SUP-'.str_pad($record->id, env('ID_PAD_LENGTH', 4), '0', STR_PAD_LEFT) : null)->searchable()->sortable(),
                TextColumn::make('branch.name_kh')->label(__('global.branch')),
                TextColumn::make('name_kh')->label(__('global.name_kh'))->searchable()->sortable(),
                TextColumn::make('name')->label(__('global.name_en'))->searchable()->sortable(),
                TextColumn::make('contact_phone')->label(__('global.contact_phone'))->searchable()->sortable(),
                TextColumn::make('createdBy.name')->label(__('global.created_by')),
                TextColumn::make('updatedBy.name')->label(__('global.updated_by')),
                TextColumn::make('created_at')->label(__('global.created_at')),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make()
                    ->label('')
                    ->tooltip(__('global.edit')),
            ], position: RecordActionsPosition::BeforeCells)
            ->toolbarActions([
                // BulkActionGroup::make([
                //     DeleteBulkAction::make(),
                // ]),
            ]);
    }
}
