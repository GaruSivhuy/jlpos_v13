<?php

namespace App\Filament\Resources\InventoryAdjustments\Tables;

use App\Stevebauman\Inventory\Exceptions\StockNotFoundException;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Grid;
use Filament\Support\Enums\Size;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Enums\RecordActionsPosition;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class InventoryAdjustmentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('id', 'desc')
            ->columns([
                TextColumn::make('id')->label(__('global.id')),
                TextColumn::make('adjustment_code')->label(__('global.adjustment_code'))->searchable()->sortable(),
                TextColumn::make('location.name_kh')->label(__('global.location')),
                TextColumn::make('status')->label(__('global.status'))->searchable()->sortable()
                    ->formatStateUsing(fn ($state) => $state == 1 ? __('global.stock_submit') : __('global.stock_not_submit')),
                TextColumn::make('note')->label(__('global.remark')),
                TextColumn::make('createdBy.name')->label(__('global.created_by')),
                TextColumn::make('updatedBy.name')->label(__('global.updated_by')),
                TextColumn::make('branch.name_kh')->label(__('global.branch')),
                TextColumn::make('created_at')->label(__('global.created_at')),
                TextColumn::make('updated_at')->label(__('global.updated_at')),
            ])
            ->filters([
                static::getDataFilter(),
            ], layout: FiltersLayout::AboveContent)
            ->filtersFormWidth('4xl')
            ->filtersFormColumns(1)
            ->filtersFormMaxHeight('400px')
            ->recordActions([
                ActionGroup::make([
                    EditAction::make()
                        ->label(__('global.edit'))
                        ->visible(fn ($record) => $record->status == 0),
                    static::getSubmitStockAction(),
                ])
                    ->label('សកម្មភាព')
                    ->button()
                    ->icon('heroicon-m-ellipsis-vertical')
                    ->size(Size::Small),
            ], position: RecordActionsPosition::BeforeCells)
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    protected static function getSubmitStockAction(): Action
    {
        return Action::make('submit')
            ->label(__('global.submit_stock'))
            ->icon('heroicon-o-shopping-cart')
            ->color('success')
            ->visible(fn ($record) => $record->status == 0 && $record->inventoryAdjustmentDetails()->exists())
            ->requiresConfirmation()
            ->modalHeading(__('global.submit_stock'))
            ->modalFooterActions(fn ($action) => [
                $action->getModalSubmitAction()->color('primary'),
                $action->getModalCancelAction()->color('danger'),
            ])
            ->action(function ($record) {
                DB::beginTransaction();

                try {
                    $location = $record->location;

                    foreach ($record->inventoryAdjustmentDetails as $detail) {
                        $product = $detail->product;
                        $metric = $detail->metric;
                        $quantity = $detail->qty * $metric->qty;

                        if ($detail->adjustment_type == 1) {
                            $reason = 'Inventory adjustment by added item to stock '.$location->name.'-'.$location->name_kh.' on '.now()->format('Y-m-d h:i:s');

                            try {
                                $product->putToLocation($quantity, $location, null, $reason);
                            } catch (StockNotFoundException $e) {
                                $product->createStockOnLocation($quantity, $location, null, $reason);
                            }
                        } else {
                            $reason = 'Inventory adjustment by minus item from stock '.$location->name.'-'.$location->name_kh.' on '.now()->format('Y-m-d h:i:s');

                            $product->takeFromLocation($quantity, $location, null, $reason);
                        }
                    }

                    $record->update([
                        'status' => 1,
                        'user_updated' => auth()->id(),
                    ]);

                    DB::commit();

                    Notification::make()
                        ->success()
                        ->title('ជោគជ័យ')
                        ->body('ទិន្នន័យត្រូវបានរក្សាទុកដោយជោគជ័យ')
                        ->send();
                } catch (StockNotFoundException $e) {
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

    protected static function getDataFilter(): Filter
    {
        return Filter::make('filter_data')
            ->columnSpan(3)
            ->schema([
                Grid::make(2)
                    ->schema([
                        DatePicker::make('from_date')
                            ->label(__('global.from_date'))
                            ->native(false)
                            ->suffixIcon('heroicon-o-calendar')
                            ->displayFormat('d-m-Y')
                            ->placeholder('DD-MM-YYYY'),
                        DatePicker::make('to_date')
                            ->label(__('global.to_date'))
                            ->native(false)
                            ->suffixIcon('heroicon-o-calendar')
                            ->displayFormat('d-m-Y')
                            ->placeholder('DD-MM-YYYY'),
                    ]),
            ])
            ->query(function (Builder $query, array $data): Builder {
                if (! empty($data['from_date']) && ! empty($data['to_date'])) {
                    $fdate = Carbon::parse($data['from_date'])->format('Y-m-d');
                    $tdate = Carbon::parse($data['to_date'])->format('Y-m-d');
                    $column_search = 'created_at';

                    $query->where($column_search, '>=', $fdate.' 00:00:00');
                    $query->where($column_search, '<=', $tdate.' 23:59:59');
                }

                return $query;
            });
    }
}
