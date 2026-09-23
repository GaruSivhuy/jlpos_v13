<?php

namespace App\Filament\Resources\Control\PaymentGateways\Schemas;

use App\Filament\Resources\Control\Schemas\BranchSelect;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Validation\Rules\Unique;

class PaymentGatewayForm
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
                                TextInput::make('name')
                                    ->required()
                                    ->maxLength(255)
                                    ->label(__('global.payment_gateway_name'))
                                    ->unique(
                                        table: 'payment_gateway',
                                        column: 'name',
                                        ignoreRecord: true,
                                        modifyRuleUsing: fn (Unique $rule, Get $get) => $rule->where('branch_id', $get('branch_id')),
                                    )
                                    ->validationMessages([
                                        'required' => __('validation.required', ['attribute' => __('global.payment_gateway_name')]),
                                        'unique' => __('validation.unique', ['attribute' => __('global.payment_gateway_name')]),
                                    ]),
                                BranchSelect::make(),
                            ]),
                    ]),
            ]);
    }
}
