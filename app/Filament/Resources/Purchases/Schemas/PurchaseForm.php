<?php

namespace App\Filament\Resources\Purchases\Schemas;

use App\Filament\Resources\Purchases\RelationManagers\PurchaseDetailsRelationManager;
use App\Models\Branch;
use App\Models\Location;
use App\Models\Purchase;
use App\Models\Supplier;
use Filament\Forms\Components\DatePicker;
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

class PurchaseForm
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
                                        TextInput::make('po_code')
                                            ->label(__('global.po_code'))
                                            ->required()
                                            ->default(fn () => strtoupper(uniqid('PO')))
                                            ->readOnly(),
                                        DatePicker::make('po_date')
                                            ->label(__('global.po_date'))
                                            ->required()
                                            ->native(false)
                                            ->displayFormat('d/m/Y'),
                                        Select::make('location_id')
                                            ->label(__('global.purchase_location'))
                                            ->searchable()
                                            ->required()
                                            ->options(fn (Get $get) => $get('branch_id') ? Location::select(DB::raw('CONCAT(name_kh) AS location_name'), 'id')->where('branch_id', $get('branch_id'))->pluck('location_name', 'id')->toArray() : [])
                                            ->placeholder(__('global.select').' '.__('global.purchase_location')),
                                        TextInput::make('discount')
                                            ->label(__('global.discount'))
                                            ->numeric(),
                                        Textarea::make('remark')
                                            ->label(__('global.remark'))
                                            ->rows(4),
                                    ]),
                                Grid::make()
                                    ->columns(1)
                                    ->inlineLabel()
                                    ->schema([
                                        Select::make('supplier_id')
                                            ->label(__('global.supplier'))
                                            ->searchable()
                                            ->options(fn (Get $get) => $get('branch_id') ? Supplier::where('branch_id', $get('branch_id'))->pluck('name', 'id')->toArray() : [])
                                            ->placeholder(__('global.select').' '.__('global.supplier')),
                                    ]),
                            ]),
                    ]),
                Section::make()
                    ->heading(__('global.purchase_item'))
                    ->columnSpanFull()
                    ->visible(fn (?Purchase $record) => $record !== null)
                    ->schema([
                        RelationManager::make()
                            ->manager(PurchaseDetailsRelationManager::class)
                            ->lazy(false)
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
