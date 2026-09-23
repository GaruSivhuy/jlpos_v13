<?php

namespace App\Filament\Resources\Control\OverMoney\Tables;

use App\Filament\Resources\Control\OverMoney\OverMoneyResource;
use App\Filament\Resources\Control\OverMoney\Schemas\OverMoneyForm;
use App\Filament\Resources\Control\Tables\DateRangeFilter;
use App\Models\OverMoney;
use Filament\Actions\EditAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Enums\RecordActionsPosition;
use Filament\Tables\Table;

class OverMoneyTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('id', 'desc')
            ->columns([
                TextColumn::make('id')
                    ->label(__('global.id'))
                    ->sortable()
                    ->state(fn ($record) => 'OVER-'.str_pad($record->id, env('ID_PAD_LENGTH', 8), '0', STR_PAD_LEFT)),
                TextColumn::make('over_money_date')->label(__('global.date'))->date('d-m-Y')->sortable(),
                TextColumn::make('over_amount')->label(__('global.amount'))->numeric()->sortable(),
                TextColumn::make('over_money_type')
                    ->label(__('global.currency'))
                    ->formatStateUsing(fn ($state) => OverMoneyForm::currencyOptions()[$state] ?? null),
                TextColumn::make('branch.name_kh')->label(__('global.branch')),
                TextColumn::make('user_create.name')->label(__('global.created_by')),
                TextColumn::make('user_updated.name')->label(__('global.updated_by')),
                TextColumn::make('created_at')->label(__('global.created_at'))->dateTime('d-m-Y H:i:s')->sortable(),
                TextColumn::make('updated_at')->label(__('global.updated_at'))->dateTime('d-m-Y H:i:s')->sortable(),
            ])
            ->filters([
                DateRangeFilter::make('over_money_date'),
            ], layout: FiltersLayout::AboveContent)
            ->filtersFormWidth('4xl')
            ->filtersFormColumns(1)
            ->filtersFormMaxHeight('400px')
            ->recordActions([
                EditAction::make()
                    ->tooltip(__('global.edit'))
                    ->hiddenLabel()
                    ->icon(Heroicon::PencilSquare)
                    ->visible(fn (OverMoney $record): bool => OverMoneyResource::canEdit($record)),
            ], position: RecordActionsPosition::BeforeCells);
    }
}
