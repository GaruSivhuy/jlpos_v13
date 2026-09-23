<?php

namespace App\Filament\Resources\Control\Locations\Tables;

use Filament\Actions\EditAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\RecordActionsPosition;
use Filament\Tables\Table;

class LocationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('id', 'desc')
            ->columns([
                TextColumn::make('id')
                    ->label(__('global.id'))
                    ->sortable()
                    ->state(fn ($record) => 'LOC-'.str_pad($record->id, env('ID_PAD_LENGTH', 4), '0', STR_PAD_LEFT)),
                TextColumn::make('name')->label(__('global.lname'))->searchable()->sortable(),
                TextColumn::make('name_kh')->label(__('global.lname_kh'))->searchable()->sortable(),
                TextColumn::make('branch.name_kh')->label(__('global.branch')),
                TextColumn::make('user_create.name')->label(__('global.created_by')),
                TextColumn::make('user_update.name')->label(__('global.updated_by')),
                TextColumn::make('created_at')->label(__('global.created_at'))->dateTime('d-m-Y H:i:s')->sortable(),
                TextColumn::make('updated_at')->label(__('global.updated_at'))->dateTime('d-m-Y H:i:s')->sortable(),
            ])
            ->recordActions([
                EditAction::make()
                    ->tooltip(__('global.edit'))
                    ->hiddenLabel()
                    ->icon(Heroicon::PencilSquare),
            ], position: RecordActionsPosition::BeforeCells);
    }
}
