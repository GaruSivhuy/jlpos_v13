<?php

namespace App\Filament\Resources\InventoryAdjustments\RelationManagers;

use App\Models\Metric;
use App\Models\Product;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Alignment;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class InventoryAdjustmentDetailsRelationManager extends RelationManager
{
    protected static string $relationship = 'inventoryAdjustmentDetails';

    protected static ?string $title = '';

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
                            ->options(function () {
                                $locationId = $this->getOwnerRecord()->location_id;

                                return Product::query()
                                    ->branch()
                                    ->whereHas('stocks', fn ($query) => $query->where('location_id', $locationId)->where('quantity', '>', 0))
                                    ->pluck('name_kh', 'id')
                                    ->toArray();
                            })
                            ->searchable()
                            ->live(onBlur: true)
                            ->required()
                            ->placeholder(__('global.select').' '.__('global.pname_kh')),
                        Select::make('metric_id')
                            ->label(__('global.mname'))
                            ->options(function (Get $get) {
                                $product = Product::find($get('inventory_id'));

                                if (! $product) {
                                    return [];
                                }

                                $locationId = $this->getOwnerRecord()->location_id;

                                $metricIds = $product->stocks()
                                    ->where('location_id', $locationId)
                                    ->where('quantity', '>', 0)
                                    ->pluck('metric_id');

                                return Metric::whereIn('id', $metricIds)->pluck('name_kh', 'id')->toArray();
                            })
                            ->searchable()
                            ->required()
                            ->placeholder(__('global.select').' '.__('global.mname')),
                        Select::make('adjustment_type')
                            ->label(__('global.adjustment_type'))
                            ->options([
                                1 => __('global.add_stock'),
                                2 => __('global.deduct_stock'),
                            ])
                            ->required()
                            ->placeholder(__('global.select').' '.__('global.adjustment_type')),
                        TextInput::make('qty')
                            ->label(__('global.quantity'))
                            ->numeric()
                            ->required(),
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
