<?php

namespace App\Filament\Resources\Remarks\Schemas;

use App\Models\Branch;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules\Unique;

class RemarkForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                ->columns(5)
                ->columnSpanFull()
                ->schema([
                    Grid::make()
                    ->columns(1)
                    ->columnSpan(3)
                    ->inlineLabel()
                    ->schema([
                        Select::make('branch_id')
                            ->label(__("global.branch"))
                            ->searchable()
                            ->required()
                            ->live(onBlur:true)
                            ->options(auth()->user()->is_admin ? Branch::select(DB::raw('CONCAT(name_kh) AS branch_name'), 'branch.id')->pluck('branch_name', 'id')->toArray() : auth()->user()->branch()->select(DB::raw('CONCAT(name_kh) AS branch_name'), 'branch.id')->pluck('branch_name', 'id')->toArray()),
                        TextInput::make('name_kh')
                            ->required()
                            ->label(__("global.remarkable"))
                            ->unique(
                                table: 'remarks',
                                column: 'name_kh',
                                ignoreRecord: true, // automatically excludes current record's id on edit
                                modifyRuleUsing: function (Unique $rule, Get $get) {
                                    return $rule->where('branch_id', $get('branch_id'));
                                }
                            )
                            ->validationMessages([
                                'required' => __('validation.required', ['attribute' => __('global.remarkable')]),
                                'unique'   => __('validation.unique', ['attribute' => __('global.remarkable')]),
                            ]),
                            
                    ])
                ])
            ]);
    }
}
