<?php

namespace App\Filament\Resources\Remarks\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\RecordActionsPosition;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class RemarksTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->label(__("global.id"))
                ->state(fn($record) => $record ? "RMK-" . str_pad($record->id, env("ID_PAD_LENGTH", 4), "0", STR_PAD_LEFT) : null),
                TextColumn::make('name_kh')->label(__("global.remarkable")),
                TextColumn::make('user_create.name')->label(__("global.created_by")),
                TextColumn::make('user_update.name')->label(__("global.updated_by")),
                TextColumn::make('user_create.name')->label(__("global.created_by")),
                TextColumn::make('branch.name_kh')->label(__("global.branch")),
                TextColumn::make('created_at')->label(__("global.created_at")),
                TextColumn::make('updated_at')->label(__("global.updated_at")),
            ])
            ->filters([
                // TrashedFilter::make(),
            ])
            ->recordActions([
                EditAction::make()
                    ->tooltip(__("global.edit"))
                    ->hiddenLabel()
                    ->icon(Heroicon::PencilSquare),
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
