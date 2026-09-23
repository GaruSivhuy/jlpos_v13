<?php

namespace App\Filament\Resources\Control\ChangeProducts\Schemas;

use App\Filament\Resources\Control\Schemas\BranchSelect;
use App\Models\ChangeProduct;
use App\Models\Location;
use App\Models\Product;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;

class ChangeProductForm
{
    /**
     * @return array<int, string>
     */
    public static function typeOptions(): array
    {
        return [
            ChangeProduct::TYPE_ITEM => __('global.change_product_type_item'),
            ChangeProduct::TYPE_CASH => __('global.change_product_type_cash'),
            ChangeProduct::TYPE_EARRING => __('global.change_product_type_earring'),
        ];
    }

    public static function totalAmount(mixed $qty, mixed $amount): ?float
    {
        if (! is_numeric($qty) || ! is_numeric($amount)) {
            return null;
        }

        return (float) $qty * (float) $amount;
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
                                BranchSelect::make()
                                    ->afterStateUpdated(function (Set $set): void {
                                        $set('location_id', null);
                                        $set('inventory_id', null);
                                        $set('metric_id', null);
                                    }),
                                Select::make('change_product_type')
                                    ->label(__('global.change_product_type'))
                                    ->required()
                                    ->searchable()
                                    ->live()
                                    ->options(static::typeOptions())
                                    ->validationMessages([
                                        'required' => __('validation.required', ['attribute' => __('global.change_product_type')]),
                                    ]),
                                TextInput::make('change_description')
                                    ->label(__('global.change_description'))
                                    ->maxLength(255),
                                DatePicker::make('change_product_date')
                                    ->label(__('global.change_product_date'))
                                    ->required()
                                    ->default(today())
                                    ->native(false)
                                    ->suffixIcon('heroicon-o-calendar')
                                    ->displayFormat('d-m-Y')
                                    ->placeholder('DD-MM-YYYY')
                                    ->validationMessages([
                                        'required' => __('validation.required', ['attribute' => __('global.change_product_date')]),
                                    ]),
                                Select::make('location_id')
                                    ->label(__('global.from_location'))
                                    ->searchable()
                                    ->required(fn (Get $get): bool => static::isItemType($get))
                                    ->options(fn (Get $get): array => Location::query()
                                        ->branch()
                                        ->when($get('branch_id'), fn ($query, $branchId) => $query->where('branch_id', $branchId))
                                        ->get()
                                        ->mapWithKeys(fn (Location $location): array => [$location->id => collect([$location->name, $location->name_kh])->filter()->implode(' - ')])
                                        ->all())
                                    ->validationMessages([
                                        'required' => __('global.change_product_stock_location_required'),
                                    ]),
                                Select::make('inventory_id')
                                    ->label(__('global.product'))
                                    ->searchable()
                                    ->live()
                                    ->required(fn (Get $get): bool => static::isItemType($get))
                                    ->options(fn (Get $get): array => Product::query()
                                        ->branch()
                                        ->when($get('branch_id'), fn ($query, $branchId) => $query->where('branch_id', $branchId))
                                        ->pluck('name_kh', 'id')
                                        ->all())
                                    ->afterStateUpdated(fn (Set $set) => $set('metric_id', null))
                                    ->validationMessages([
                                        'required' => __('validation.required', ['attribute' => __('global.product')]),
                                    ]),
                                Select::make('metric_id')
                                    ->label(__('global.metric'))
                                    ->searchable()
                                    ->required(fn (Get $get): bool => filled($get('inventory_id')))
                                    ->options(fn (Get $get): array => Product::query()->find($get('inventory_id'))?->metrics->pluck('name_kh', 'id')->all() ?? [])
                                    ->validationMessages([
                                        'required' => __('validation.required', ['attribute' => __('global.metric')]),
                                    ]),
                                TextInput::make('qty')
                                    ->label(__('global.qty'))
                                    ->required()
                                    ->integer()
                                    ->minValue(1)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn (Get $get, Set $set) => static::recalculate($get, $set))
                                    ->validationMessages([
                                        'required' => __('validation.required', ['attribute' => __('global.qty')]),
                                    ]),
                                TextInput::make('amount')
                                    ->label(__('global.amount'))
                                    ->required()
                                    ->numeric()
                                    ->minValue(0)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn (Get $get, Set $set) => static::recalculate($get, $set))
                                    ->validationMessages([
                                        'required' => __('validation.required', ['attribute' => __('global.amount')]),
                                    ]),
                                TextInput::make('total_amount')
                                    ->label(__('global.total_amount'))
                                    ->readOnly()
                                    ->dehydrated(false),
                            ]),
                    ]),
            ]);
    }

    protected static function isItemType(Get $get): bool
    {
        return (int) $get('change_product_type') === ChangeProduct::TYPE_ITEM;
    }

    protected static function recalculate(Get $get, Set $set): void
    {
        $set('total_amount', static::totalAmount($get('qty'), $get('amount')));
    }
}
