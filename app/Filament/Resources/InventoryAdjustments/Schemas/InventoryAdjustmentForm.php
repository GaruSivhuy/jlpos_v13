<?php

namespace App\Filament\Resources\InventoryAdjustments\Schemas;

use App\Filament\Resources\InventoryAdjustments\RelationManagers\InventoryAdjustmentDetailsRelationManager;
use App\Models\Branch;
use App\Models\InventoryAdjustment;
use App\Models\Location;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\DB;
use Njxqlus\Filament\Components\Forms\RelationManager;

class InventoryAdjustmentForm
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
                                        TextInput::make('adjustment_code')
                                            ->label(__('global.adjustment_code'))
                                            ->required()
                                            ->default(fn () => strtoupper(uniqid('IA')))
                                            ->readOnly(),
                                        Select::make('location_id')
                                            ->label(__('global.location'))
                                            ->searchable()
                                            ->required()
                                            ->options(fn (Get $get) => $get('branch_id') ? Location::select(DB::raw('CONCAT(name_kh) AS location_name'), 'id')->where('branch_id', $get('branch_id'))->pluck('location_name', 'id')->toArray() : [])
                                            ->placeholder(__('global.select').' '.__('global.location')),
                                    ]),
                                Grid::make()
                                    ->columns(1)
                                    ->inlineLabel()
                                    ->schema([
                                        Textarea::make('note')
                                            ->label(__('global.remark'))
                                            ->rows(5),
                                    ]),
                            ]),
                    ]),
                Section::make()
                    ->heading(__('global.adjustment_item'))
                    ->columnSpanFull()
                    ->visible(fn (?InventoryAdjustment $record) => $record !== null)
                    ->schema([
                        RelationManager::make()
                            ->manager(InventoryAdjustmentDetailsRelationManager::class)
                            ->lazy(false)
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
