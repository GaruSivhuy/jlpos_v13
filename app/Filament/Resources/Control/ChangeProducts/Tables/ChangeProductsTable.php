<?php

namespace App\Filament\Resources\Control\ChangeProducts\Tables;

use App\Filament\Resources\Control\ChangeProducts\ChangeProductResource;
use App\Filament\Resources\Control\ChangeProducts\Schemas\ChangeProductForm;
use App\Filament\Resources\Control\Tables\DateRangeFilter;
use App\Models\ChangeProduct;
use App\Stevebauman\Inventory\Exceptions\NotEnoughStockException;
use App\Stevebauman\Inventory\Exceptions\StockNotFoundException;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Enums\RecordActionsPosition;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class ChangeProductsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('id', 'desc')
            ->columns([
                TextColumn::make('id')
                    ->label(__('global.id'))
                    ->sortable()
                    ->state(fn ($record) => 'CHPRO-'.str_pad($record->id, env('ID_PAD_LENGTH', 8), '0', STR_PAD_LEFT)),
                TextColumn::make('change_product_date')->label(__('global.change_product_date'))->date('d-m-Y')->sortable(),
                TextColumn::make('change_description')->label(__('global.change_description'))->searchable(),
                TextColumn::make('change_product_type')
                    ->label(__('global.change_product_type'))
                    ->formatStateUsing(fn ($state) => ChangeProductForm::typeOptions()[$state] ?? null),
                TextColumn::make('inventories.name_kh')->label(__('global.product')),
                TextColumn::make('metrics.name_kh')->label(__('global.metric')),
                TextColumn::make('qty')->label(__('global.qty'))->sortable(),
                TextColumn::make('amount')->label(__('global.amount'))->numeric()->sortable(),
                TextColumn::make('total_amount')->label(__('global.total_amount'))->numeric()->sortable(),
                TextColumn::make('submit_status')
                    ->label(__('global.status'))
                    ->formatStateUsing(fn ($state, ChangeProduct $record): string => (int) $record->change_product_type !== ChangeProduct::TYPE_ITEM
                        ? '-'
                        : ((int) $state === 1 ? __('global.stock_submit') : __('global.stock_not_submit'))),
                TextColumn::make('branch.name_kh')->label(__('global.branch')),
                TextColumn::make('user_create.name')->label(__('global.created_by')),
                TextColumn::make('user_updated.name')->label(__('global.updated_by')),
                TextColumn::make('created_at')->label(__('global.created_at'))->dateTime('d-m-Y H:i:s')->sortable(),
                TextColumn::make('updated_at')->label(__('global.updated_at'))->dateTime('d-m-Y H:i:s')->sortable(),
            ])
            ->filters([
                DateRangeFilter::make('change_product_date'),
                SelectFilter::make('change_product_type')
                    ->label(__('global.change_product_type'))
                    ->options(ChangeProductForm::typeOptions()),
            ], layout: FiltersLayout::AboveContent)
            ->filtersFormWidth('4xl')
            ->filtersFormColumns(1)
            ->filtersFormMaxHeight('400px')
            ->recordActions([
                EditAction::make()
                    ->tooltip(__('global.edit'))
                    ->hiddenLabel()
                    ->icon(Heroicon::PencilSquare)
                    ->visible(fn (ChangeProduct $record): bool => ChangeProductResource::canEdit($record)),
                static::getSubmitStockAction(),
            ], position: RecordActionsPosition::BeforeCells)
            ->toolbarActions([
                BulkActionGroup::make([
                    static::getPrintSelectedAction(),
                ]),
            ]);
    }

    protected static function getSubmitStockAction(): Action
    {
        return Action::make('submitStock')
            ->tooltip(__('global.submit_stock'))
            ->hiddenLabel()
            ->icon('heroicon-o-shopping-cart')
            ->color('success')
            ->visible(fn (ChangeProduct $record): bool => (int) $record->change_product_type === ChangeProduct::TYPE_ITEM
                && (int) $record->submit_status === 0)
            ->requiresConfirmation()
            ->modalHeading(__('global.submit_stock'))
            ->modalFooterActions(fn ($action) => [
                $action->getModalSubmitAction()->color('primary'),
                $action->getModalCancelAction()->color('danger'),
            ])
            ->action(function (ChangeProduct $record): void {
                DB::beginTransaction();

                try {
                    $location = $record->location;
                    $metric = $record->metrics;
                    $quantity = $record->qty * $metric->qty;

                    $taken = $record->inventories->takeFromLocation(
                        $quantity,
                        $location,
                        $metric,
                        'Take to Change Product from stock '.$location->name_kh.' on '.now()->format('Y-m-d h:i:s'),
                    );

                    // The inventory package swallows save errors and reports them as `false`.
                    if ($taken === false) {
                        throw new NotEnoughStockException(__('global.stock_take_failed'));
                    }

                    $record->update([
                        'submit_status' => 1,
                        'user_update' => auth()->id(),
                    ]);

                    DB::commit();

                    Notification::make()
                        ->success()
                        ->title('ជោគជ័យ')
                        ->body('ទិន្នន័យត្រូវបានរក្សាទុកដោយជោគជ័យ')
                        ->send();
                } catch (StockNotFoundException|NotEnoughStockException $e) {
                    DB::rollBack();

                    Notification::make()
                        ->danger()
                        ->title(__('global.error'))
                        ->body($e->getMessage())
                        ->send();
                } catch (\Throwable $e) {
                    DB::rollBack();

                    throw $e;
                }
            });
    }

    protected static function getPrintSelectedAction(): BulkAction
    {
        return BulkAction::make('printSelected')
            ->label(__('global.print_selected'))
            ->icon(Heroicon::OutlinedPrinter)
            ->deselectRecordsAfterCompletion()
            ->action(function (Collection $records, $livewire): void {
                $ids = $records->modelKeys();
                sort($ids);

                $livewire->redirect(route('change-product.receipt', ['ids' => $ids]));
            });
    }
}
