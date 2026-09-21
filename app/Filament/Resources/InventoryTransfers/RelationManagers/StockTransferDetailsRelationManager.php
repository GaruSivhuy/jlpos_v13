<?php

namespace App\Filament\Resources\InventoryTransfers\RelationManagers;

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

class StockTransferDetailsRelationManager extends RelationManager
{
    protected static string $relationship = 'stockTransferDetails';

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
                            ->options(fn () => Product::query()->branch()->pluck('name_kh', 'id')->toArray())
                            ->searchable()
                            ->live(onBlur: true)
                            ->required()
                            ->placeholder(__('global.select').' '.__('global.pname_kh')),
                        Select::make('metric_id')
                            ->label(__('global.mname'))
                            ->options(function (Get $get) {
                                $product = Product::find($get('inventory_id'));

                                return $product
                                    ? $product->metrics()->pluck('metrics.name_kh', 'metrics.id')->toArray()
                                    : [];
                            })
                            ->searchable()
                            ->required()
                            ->placeholder(__('global.select').' '.__('global.mname')),
                        Select::make('transfer_metric_id')
                            ->label(__('global.mname_transfer'))
                            ->options(function (Get $get) {
                                $product = Product::find($get('inventory_id'));

                                return $product
                                    ? $product->metrics()->pluck('metrics.name_kh', 'metrics.id')->toArray()
                                    : [];
                            })
                            ->searchable()
                            ->placeholder(__('global.select').' '.__('global.mname_transfer')),
                        TextInput::make('qty')
                            ->label(__('global.transfer_quantity'))
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
                TextColumn::make('qty')->label(__('global.transfer_quantity')),
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
