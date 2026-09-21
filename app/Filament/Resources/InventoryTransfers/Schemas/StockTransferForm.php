<?php

namespace App\Filament\Resources\InventoryTransfers\Schemas;

use App\Filament\Resources\InventoryTransfers\RelationManagers\StockTransferDetailsRelationManager;
use App\Models\Branch;
use App\Models\Location;
use App\Models\StockTransfer;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\DB;
use Njxqlus\Filament\Components\Forms\RelationManager;

class StockTransferForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->columns(1)
                    ->columnSpanFull()
                    ->schema([
                        Hidden::make('status')
                            ->default(0),
                        Section::make()
                            ->columns(2)
                            ->columnSpanFull()
                            ->schema([
                                Grid::make()
                                    ->columns(1)
                                    ->inlineLabel()
                                    ->schema([
                                        Select::make('branch_id')
                                            ->label(__('global.branch'))
                                            ->searchable()
                                            ->required()
                                            ->live(onBlur: true)
                                            ->options(auth()->user()->is_admin ? Branch::select(DB::raw('CONCAT(name_kh) AS branch_name'), 'branch.id')->pluck('branch_name', 'id')->toArray() : auth()->user()->branch()->select(DB::raw('CONCAT(name_kh) AS branch_name'), 'branch.id')->pluck('branch_name', 'id')->toArray()),
                                        TextInput::make('st_code')
                                            ->label(__('global.st_code'))
                                            ->required()
                                            ->default(fn () => strtoupper(uniqid('ST')))
                                            ->readOnly(),
                                    ]),
                                Grid::make()
                                    ->columns(1)
                                    ->inlineLabel()
                                    ->schema([
                                        Select::make('from_location')
                                            ->label(__('global.from_location'))
                                            ->searchable()
                                            ->required()
                                            ->live(onBlur: true)
                                            ->options(fn (Get $get) => $get('branch_id') ? Location::select(DB::raw('CONCAT(name_kh) AS location_name'), 'id')->where('branch_id', $get('branch_id'))->pluck('location_name', 'id')->toArray() : [])
                                            ->placeholder(__('global.select').' '.__('global.from_location')),
                                        Select::make('to_location')
                                            ->label(__('global.to_location'))
                                            ->searchable()
                                            ->required()
                                            ->rules(['different:from_location'])
                                            ->validationMessages([
                                                'different' => __('validation.different', ['attribute' => __('global.to_location'), 'other' => __('global.from_location')]),
                                            ])
                                            ->options(fn (Get $get) => $get('branch_id') ? Location::select(DB::raw('CONCAT(name_kh) AS location_name'), 'id')->where('branch_id', $get('branch_id'))->pluck('location_name', 'id')->toArray() : [])
                                            ->placeholder(__('global.select').' '.__('global.to_location')),
                                    ]),
                            ]),
                    ]),
                Section::make()
                    ->heading(__('global.transfer_item'))
                    ->columnSpanFull()
                    ->visible(fn (?StockTransfer $record) => $record !== null)
                    ->schema([
                        RelationManager::make()
                            ->manager(StockTransferDetailsRelationManager::class)
                            ->lazy(false)
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
