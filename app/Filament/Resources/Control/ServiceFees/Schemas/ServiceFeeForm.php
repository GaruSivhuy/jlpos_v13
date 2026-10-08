<?php

namespace App\Filament\Resources\Control\ServiceFees\Schemas;

use App\Filament\Resources\Control\Schemas\BranchSelect;
use App\Models\PaymentGateway;
use App\Models\ServiceFee;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;

class ServiceFeeForm
{
    /**
     * @return array<int, string>
     */
    public static function typeOptions(): array
    {
        return [
            ServiceFee::TYPE_USD => __('global.service_type_usd'),
            ServiceFee::TYPE_RIEL => __('global.service_type_riel'),
        ];
    }

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
                                BranchSelect::make(),
                                Select::make('service_type')
                                    ->label(__('global.service_type'))
                                    ->required()
                                    ->searchable()
                                    ->live()
                                    ->options(static::typeOptions())
                                    ->afterStateUpdated(function (Set $set): void {
                                        $set('amount', null);
                                    })
                                    ->validationMessages([
                                        'required' => __('validation.required', ['attribute' => __('global.service_type')]),
                                    ]),
                                
                                Select::make('payment_type')
                                    ->label(__('global.payment_gateway'))
                                    ->required()
                                    ->searchable()
                                    ->options(fn (Get $get): array => PaymentGateway::query()
                                        ->when($get('branch_id'), fn ($query, $branchId) => $query->where('branch_id', $branchId))
                                        ->pluck('name', 'id')
                                        ->all())
                                    ->validationMessages([
                                        'required' => __('validation.required', ['attribute' => __('global.payment_gateway')]),
                                    ]),
                                TextInput::make('amount')
                                    ->label(__('global.amount'))
                                    ->required()
                                    ->numeric()
                                    ->rule('gt:0')
                                    ->live(debounce: 500)
                                    ->suffix(fn (Get $get): ?string => match ((int) $get('service_type')) {
                                        ServiceFee::TYPE_USD => '$',
                                        ServiceFee::TYPE_RIEL => '៛',
                                        default => null,
                                    })
                                    ->validationMessages([
                                        'required' => __('validation.required', ['attribute' => __('global.amount')]),
                                    ]),
                                TextInput::make('service_fees')
                                    ->label(__('global.service_fees'))
                                    ->required()
                                    ->numeric()
                                    ->rule('gt:0')
                                    ->validationMessages([
                                        'required' => __('validation.required', ['attribute' => __('global.service_fees')]),
                                    ])
                                    ->suffix('៛'),
                            ]),
                    ]),
            ]);
    }
}
