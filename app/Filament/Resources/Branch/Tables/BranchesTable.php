<?php

namespace App\Filament\Resources\Branch\Tables;

use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\RecordActionsPosition;
use Filament\Tables\Table;

class BranchesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->label(__('global.id'))->searchable()->sortable(),
                TextColumn::make('name_en')->label(__('global.name_en'))->searchable()->sortable(),
                TextColumn::make('name_kh')->label(__('global.name_kh'))->searchable()->sortable(),
                IconColumn::make('is_active')->label(__('global.active'))->boolean(),
                TextColumn::make('created_at')->label(__('global.created_at'))->dateTime('d-m-Y H:i:s')->sortable(),
                TextColumn::make('updated_at')->label(__('global.updated_at'))->dateTime('d-m-Y H:i:s')->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make()
                    ->label('')
                    ->tooltip(__('global.edit')),
            ], position: RecordActionsPosition::BeforeCells);
    }
}
