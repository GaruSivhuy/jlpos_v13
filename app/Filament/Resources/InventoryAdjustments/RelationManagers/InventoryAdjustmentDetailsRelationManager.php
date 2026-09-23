<?php

namespace App\Filament\Resources\InventoryAdjustments\RelationManagers;

use App\Models\Location;
use App\Models\Metric;
use App\Models\Product;
use App\Stevebauman\Inventory\Exceptions\StockNotFoundException;
use Closure;
use Exception;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Alignment;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Validation\Rules\Unique;

class InventoryAdjustmentDetailsRelationManager extends RelationManager
{
    protected static string $relationship = 'inventoryAdjustmentDetails';

    protected static ?string $title = '';

    protected function getAvailableQuantity(?int $productId, ?int $metricId): ?int
    {
        if (! $productId || ! $metricId) {
            return null;
        }

        $locationId = $this->getOwnerRecord()->location_id;

        try{
            $item     = Product::find($productId);
            $location = Location::find($locationId);
            $metric   = Metric::find($metricId);
            try {
                $stock    = $item->getStockFromLocation($location);
                $quantity = $stock->quantity/$metric->qty;
                return $quantity;
            } catch (StockNotFoundException $e) {
                return 0;
            }
        }catch(Exception $e){
            Notification::make()
                ->danger()
                ->title(__("global.error"))
                ->body($e->getMessage())
                ->send();
            return 0;
        }
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(1)
                    ->columnSpanFull()
                    ->inlineLabel()
                    ->schema([
                        Select::make('inventory_id')
                            ->label(__('global.pname_kh'))
                            ->options(function (Get $get) {
                                $locationId = $this->getOwnerRecord()->location_id;
                                $query = Product::query()->branch();
                                $query->whereHas('stocks', fn ($query) => $query->where('location_id', $locationId)->where('quantity', '>', 0));
                                
                                return $query->pluck('name_kh', 'id')->toArray();
                            })
                            ->searchable()
                            ->live(onBlur: true)
                            ->required()
                            ->afterStateUpdated(function (Set $set) {
                                $set('metric_id', null);
                                $set('in_stock_quantity', null);
                            })
                            ->placeholder(__('global.select').' '.__('global.pname_kh')),
                        Select::make('metric_id')
                            ->label(__('global.mname'))
                            ->options(function (Get $get) {
                                $product = Product::find($get('inventory_id'));

                                if (! $product) {
                                    return [];
                                }
                                $metricIds = $product->metrics()->select(\DB::raw('CONCAT(name_kh, " - ", qty) AS metric_name'), 'metrics.id')->pluck('metric_name', 'metrics.id')->toArray();
                                return $metricIds;
                            })
                            ->searchable()
                            ->live()
                            ->required()
                            ->unique(
                                table: 'inventory_adjustment_detail',
                                column: 'metric_id',
                                modifyRuleUsing: fn (Unique $rule, Get $get) => $rule
                                    ->where('adjustment_id', $this->getOwnerRecord()->id)
                                    ->where('inventory_id', $get('inventory_id')),
                            )
                            ->validationMessages([
                                'unique' => __('global.duplicate_record'),
                            ])
                            ->afterStateUpdated(function (Get $get, Set $set) {
                                $set('in_stock_quantity', $this->getAvailableQuantity($get('inventory_id'), $get('metric_id')));
                            })
                            ->placeholder(__('global.select').' '.__('global.mname')),
                        TextInput::make('in_stock_quantity')
                            ->label(__('global.in_stock_quantity'))
                            ->numeric()
                            ->disabled()
                            ->dehydrated(false)
                            ->afterStateHydrated(function (TextInput $component, Get $get) {
                                $component->state($this->getAvailableQuantity($get('inventory_id'), $get('metric_id')));
                            }),
                        
                        Select::make('adjustment_type')
                            ->label(__('global.adjustment_type'))
                            ->options([
                                1 => __('global.add_stock'),
                                2 => __('global.deduct_stock'),
                            ])
                            ->required()
                            ->live()
                            ->searchable()
                            ->placeholder(__('global.select').' '.__('global.adjustment_type')),
                        TextInput::make('qty')
                            ->label(__('global.quantity'))
                            ->numeric()
                            ->required()
                            ->rules([
                                fn (Get $get): Closure => function (string $attribute, $value, Closure $fail) use ($get) {
                                    if ((int) $get('adjustment_type') !== 2) {
                                        return;
                                    }

                                    $available = $get('in_stock_quantity');

                                    if ($available !== null && (float) $value > (float) $available) {
                                        $fail(__('global.over_stock_alert_message_adjustment'));
                                    }
                                },
                            ]),
                        Textarea::make('remark')
                            ->label(__('global.remark'))
                            ->rows(3),
                    ]),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->paginated(false)
            ->columns([
                TextColumn::make('index')->label(__('global.no'))->rowIndex(),
                TextColumn::make('product.name_kh')->label(__('global.pname_kh')),
                TextColumn::make('metric.name_kh')->label(__('global.mname')),
                TextColumn::make('adjustment_type')
                    ->label(__('global.adjustment_type'))
                    ->formatStateUsing(fn ($state) => $state == 1 ? __('global.add_stock') : __('global.deduct_stock')),
                TextColumn::make('qty')->label(__('global.quantity')),
                TextColumn::make('remark')->label(__('global.remark')),
            ])
            ->emptyStateHeading(__('global.no_record_found'))
            ->filters([])
            ->headerActions([
                CreateAction::make()
                    ->label(__('global.add_item'))
                    ->modalHeading(__('global.add_item'))
                    ->modalWidth('5xl')
                    ->modalFooterActionsAlignment(Alignment::End)
                    ->modalFooterActions(fn ($action) => [
                        $action->getModalCancelAction()->color('danger'),
                        $action->getCreateAnotherAction(),
                        $action->getModalSubmitAction()->color('primary'),
                    ]),
            ])
            ->recordActions([
                EditAction::make()
                    ->modalHeading(__('global.edit'))
                    ->modalWidth('5xl')
                    ->modalFooterActionsAlignment(Alignment::End)
                    ->modalSubmitAction(fn ($action) => $action->color('primary'))
                    ->modalCancelAction(fn ($action) => $action->color('danger')),
                DeleteAction::make()
                    ->modalHeading(__('global.delete_title'))
                    ->modalDescription(__('global.delete_text'))
                    ->modalSubmitActionLabel(__('global.delete'))
                    ->modalCancelActionLabel(__('global.cancel')),
            ]);
    }
}
