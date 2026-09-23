<?php

namespace App\Filament\Resources\Sale\Tables;

use App\Filament\Resources\Inventories\Tables\Concerns\HasDateRangeFilter;
use App\Models\Category;
use App\Models\InvoiceItem;
use App\Models\MainCategory;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class InvoiceDetailsTable
{
    use HasDateRangeFilter;

    public static function configure(Table $table): Table
    {
        return $table
            ->query(
                InvoiceItem::query()
                    ->with(['invoice', 'product', 'metric'])
                    ->where('invoice_items.status', InvoiceItem::PAID)
                    ->whereHas('invoice', fn (Builder $invoice) => $invoice->branch())
            )
            ->defaultSort('id', 'desc')
            ->columns([
                TextColumn::make('id')
                    ->label(__('global.invoice_code'))
                    ->state(fn (InvoiceItem $record) => $record->invoice?->invoice_code),
                TextColumn::make('product.pbar_code')->label(__('global.pbar_code'))->searchable(),
                TextColumn::make('product.name_kh')->label(__('global.pname_kh'))->searchable(),
                TextColumn::make('metric.name_kh')->label(__('global.mname')),
                TextColumn::make('quantity')
                    ->label(__('global.quantity'))
                    ->numeric(0)
                    ->summarize(Sum::make()->numeric(0)->label(__('global.quantity'))),
                TextColumn::make('amount')
                    ->label(__('global.amount'))
                    ->numeric(2)
                    ->summarize(Sum::make()->numeric(2)->label(__('global.total_sale'))),
                TextColumn::make('discount')->label(__('global.discount'))->numeric(2),
                TextColumn::make('created_at')->label(__('global.created_at'))->dateTime('d/m/Y H:i'),
            ])
            ->filters([
                static::getDataFilter(),
            ], layout: FiltersLayout::AboveContent)
            ->filtersFormWidth('4xl')
            ->filtersFormColumns(1)
            ->filtersFormMaxHeight('400px');
    }

    protected static function getDataFilter(): Filter
    {
        return Filter::make('filter_data')
            ->columnSpan(3)
            ->schema([
                Grid::make(4)
                    ->schema([
                        ...static::getDateRangeFilterFields(),
                        Select::make('date_type')
                            ->label(__('global.date_type'))
                            ->options([
                                '0' => __('global.date_type_invoice'),
                                '1' => __('global.date_type_payment'),
                            ])
                            ->default('0')
                            ->selectablePlaceholder(false)
                            ->native(false),
                        Select::make('main_cat_id')
                            ->label(__('global.main_category'))
                            ->options(fn () => MainCategory::select(DB::raw('CONCAT(cat_name_kh) AS categories_name'), 'id')->branch()->pluck('categories_name', 'id')->toArray())
                            ->searchable(),
                        Select::make('category_id')
                            ->label(__('global.cname_kh'))
                            ->options(fn (Get $get) => $get('main_cat_id') ? Category::select(DB::raw('CONCAT(name_kh) AS name_kh'), 'id')->where('main_cat_id', $get('main_cat_id'))->branch()->pluck('name_kh', 'id')->toArray() : [])
                            ->searchable(),
                        TextInput::make('name_kh')
                            ->label(__('global.pname_kh')),
                        TextInput::make('pbar_code')
                            ->label(__('global.pbar_code')),
                    ]),
            ])
            ->query(function (Builder $query, array $data): Builder {
                if (($data['date_type'] ?? '0') === '1') {
                    if (! empty($data['from_date']) && ! empty($data['to_date'])) {
                        $query->whereHas('invoice', fn (Builder $invoice) => static::applyDateRangeFilter($invoice, $data, 'invoices.payment_date'));
                    }
                } else {
                    static::applyDateRangeFilter($query, $data, 'invoice_items.created_at');
                }

                if (! empty($data['main_cat_id'])) {
                    $query->whereHas('product', fn (Builder $product) => $product->where('main_cat_id', $data['main_cat_id']));
                }

                if (! empty($data['category_id'])) {
                    $query->whereHas('product', fn (Builder $product) => $product->where('category_id', $data['category_id']));
                }

                if (! empty($data['name_kh'])) {
                    $query->whereHas('product', fn (Builder $product) => $product->where(
                        fn (Builder $name) => $name->where('name_kh', 'like', '%'.$data['name_kh'].'%')->orWhere('name', 'like', '%'.$data['name_kh'].'%')
                    ));
                }

                if (! empty($data['pbar_code'])) {
                    $query->whereHas('product', fn (Builder $product) => $product->where('pbar_code', $data['pbar_code']));
                }

                return $query;
            });
    }
}
