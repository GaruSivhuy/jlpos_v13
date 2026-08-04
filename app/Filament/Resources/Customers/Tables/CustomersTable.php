<?php

namespace App\Filament\Resources\Customers\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\RecordActionsPosition;
use Filament\Tables\Table;

class CustomersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->label(__("global.cus_code"))->state(fn($record) => $record ? "CUS-" . str_pad($record->id, env("ID_PAD_LENGTH", 4), "0", STR_PAD_LEFT) : null)->searchable()->sortable(),
                TextColumn::make('branch.name_kh')->label(__("global.branch")),
                TextColumn::make('name_kh')->label(__("global.name_kh"))->searchable()->sortable(),
                TextColumn::make('name_en')->label(__("global.name_en"))->searchable()->sortable(),
                TextColumn::make('email')->label(__("global.email")),
                TextColumn::make('phone_number')->label(__("global.phone_number"))->searchable()->sortable(),
                TextColumn::make('address')->label(__("global.address")),
                TextColumn::make('createdBy.name')->label(__("global.created_by")),
                TextColumn::make('updatedBy.name')->label(__("global.updated_by")),
                TextColumn::make('created_at')->label(__("global.created_at")),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make()
                ->tooltip(__("global.edit"))
                ->hiddenLabel(),
            ], position:RecordActionsPosition::BeforeCells)
            ->toolbarActions([
                // BulkActionGroup::make([
                //     DeleteBulkAction::make(),
                // ]),
            ]);
    }
}
