<?php

namespace App\Filament\Resources\Control\ExchangeMoney\Tables;

use App\Filament\Resources\Control\ExchangeMoney\ExchangeMoneyResource;
use App\Filament\Resources\Control\ExchangeMoney\Schemas\ExchangeMoneyForm;
use App\Filament\Resources\Control\Tables\DateRangeFilter;
use App\Models\ExchangeMoney;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Enums\RecordActionsPosition;
use Filament\Tables\Table;

class ExchangeMoneyTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('id', 'desc')
            ->columns([
                TextColumn::make('id')
                    ->label(__('global.id'))
                    ->sortable()
                    ->state(fn ($record) => 'EXCMON-'.str_pad($record->id, env('ID_PAD_LENGTH', 8), '0', STR_PAD_LEFT)),
                TextColumn::make('exchange_date')->label(__('global.exchange_date'))->date('d-m-Y')->sortable(),
                TextColumn::make('amount_exchange')
                    ->label(__('global.amount'))
                    ->formatStateUsing(fn ($state, ExchangeMoney $record): ?string => $state === null ? null : match ($record->exchange_type) {
                        ExchangeMoney::USD_TO_KHR => number_format($state, 2).' $',
                        ExchangeMoney::KHR_TO_USD => number_format($state).' ៛',
                        default => null,
                    }),
                TextColumn::make('rate_exchange')
                    ->label(__('global.exchange_rate'))
                    ->formatStateUsing(fn ($state): ?string => $state === null ? null : number_format($state).' ៛'),
                TextColumn::make('total_amount')
                    ->label(__('global.total_amount'))
                    ->formatStateUsing(fn ($state, ExchangeMoney $record): ?string => $state === null ? null : match ($record->exchange_type) {
                        ExchangeMoney::USD_TO_KHR => number_format($state).' ៛',
                        ExchangeMoney::KHR_TO_USD => number_format($state, 2).' $',
                        default => null,
                    }),
                TextColumn::make('exchange_type')
                    ->label(__('global.exchange_type'))
                    ->formatStateUsing(fn ($state) => ExchangeMoneyForm::typeOptions()[$state] ?? null),
                TextColumn::make('branch.name_kh')->label(__('global.branch')),
                TextColumn::make('user_create.name')->label(__('global.created_by')),
                TextColumn::make('user_updated.name')->label(__('global.updated_by')),
                TextColumn::make('created_at')->label(__('global.created_at'))->dateTime('d-m-Y H:i:s')->sortable(),
                TextColumn::make('updated_at')->label(__('global.updated_at'))->dateTime('d-m-Y H:i:s')->sortable(),
            ])
            ->filters([
                DateRangeFilter::make('exchange_date'),
            ], layout: FiltersLayout::AboveContent)
            ->filtersFormWidth('4xl')
            ->filtersFormColumns(1)
            ->filtersFormMaxHeight('400px')
            ->recordActions([
                EditAction::make()
                    ->tooltip(__('global.edit'))
                    ->hiddenLabel()
                    ->icon(Heroicon::PencilSquare)
                    ->visible(fn (ExchangeMoney $record): bool => ExchangeMoneyResource::canEdit($record)),
                Action::make('receipt')
                    ->tooltip(__('global.receipt'))
                    ->hiddenLabel()
                    ->icon(Heroicon::OutlinedPrinter)
                    ->url(fn (ExchangeMoney $record): string => route('exchange-money.receipt', ['id' => $record->getKey()]))
                    ->openUrlInNewTab(),
            ], position: RecordActionsPosition::BeforeCells);
    }
}
