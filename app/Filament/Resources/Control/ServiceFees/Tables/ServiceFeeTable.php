<?php

namespace App\Filament\Resources\Control\ServiceFees\Tables;

use App\Filament\Resources\Control\ServiceFees\Schemas\ServiceFeeForm;
use App\Filament\Resources\Control\ServiceFees\ServiceFeeResource;
use App\Filament\Resources\Control\Tables\DateRangeFilter;
use App\Models\ServiceFee;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Enums\RecordActionsPosition;
use Filament\Tables\Table;
use Illuminate\Support\Js;

class ServiceFeeTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('id', 'desc')
            ->columns([
                TextColumn::make('id')
                    ->label(__('global.id'))
                    ->sortable()
                    ->state(fn ($record) => 'SRVFEE-'.str_pad($record->id, env('ID_PAD_LENGTH', 8), '0', STR_PAD_LEFT)),
                TextColumn::make('service_type')
                    ->label(__('global.service_type'))
                    ->formatStateUsing(fn ($state) => ServiceFeeForm::typeOptions()[$state] ?? null),
                TextColumn::make('amount')
                    ->label(__('global.amount'))
                    ->formatStateUsing(fn ($state): ?string => $state === null ? null : number_format($state, 2)),
                TextColumn::make('service_fees')
                    ->label(__('global.service_fees'))
                    ->formatStateUsing(fn ($state): ?string => $state === null ? null : number_format($state, 2)),
                TextColumn::make('paymentGateway.name')->label(__('global.payment_gateway')),
                TextColumn::make('user_create.name')->label(__('global.created_by')),
                TextColumn::make('user_updated.name')->label(__('global.updated_by')),
                TextColumn::make('created_at')->label(__('global.created_at'))->dateTime('d-m-Y H:i:s')->sortable(),
                TextColumn::make('updated_at')->label(__('global.updated_at'))->dateTime('d-m-Y H:i:s')->sortable(),
            ])
            ->filters([
                DateRangeFilter::make('created_at'),
            ], layout: FiltersLayout::AboveContent)
            ->filtersFormWidth('4xl')
            ->filtersFormColumns(1)
            ->filtersFormMaxHeight('400px')
            ->recordActions([
                EditAction::make()
                    ->tooltip(__('global.edit'))
                    ->hiddenLabel()
                    ->icon(Heroicon::PencilSquare)
                    ->visible(fn (ServiceFee $record): bool => ServiceFeeResource::canEdit($record)),
                Action::make('receipt')
                    ->tooltip(__('global.receipt'))
                    ->hiddenLabel()
                    ->icon(Heroicon::OutlinedPrinter)
                    ->alpineClickHandler(fn (ServiceFee $record): string => 'window.open('.Js::from(route('service-fee.receipt', ['id' => $record->getKey()])).", '_blank')"),
            ], position: RecordActionsPosition::BeforeCells);
    }
}
