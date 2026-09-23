<?php

namespace App\Filament\Resources\Users\Tables;

use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\RecordActionsPosition;
use Filament\Tables\Table;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->label(__('global.id'))->searchable()->sortable(),
                TextColumn::make('name')->label(__('global.user_name'))->searchable()->sortable(),
                TextColumn::make('email')->label(__('global.email'))->searchable()->sortable(),
                TextColumn::make('roles.name')->label(__('global.roles'))->badge(),
                TextColumn::make('branch.name_en')->label(__('global.branch'))->badge(),
                IconColumn::make('active')->label(__('global.active'))->boolean(),
                IconColumn::make('is_admin')->label(__('global.is_admin'))->boolean(),
                TextColumn::make('created_at')->label(__('global.created_at'))->dateTime()->sortable(),
                TextColumn::make('updated_at')->label(__('global.updated_at'))->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
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
