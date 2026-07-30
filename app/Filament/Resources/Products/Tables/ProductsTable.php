<?php

namespace App\Filament\Resources\Products\Tables;

use App\Filament\Resources\Products\Schemas\ProductDetailForm;
use App\Filament\Resources\Products\Schemas\ProductForm;
use App\Models\Category;
use App\Models\MainCategory;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\Size;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Enums\RecordActionsPosition;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ProductsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultKeySort(false)
            ->recordUrl(null)
            ->columns([
                SpatieMediaLibraryImageColumn::make('product')->collection('product')->disk('products')
                ->conversion('thumb')
                ->label(__("global.product_photo")),
                TextColumn::make('pbar_code')->label(__("global.pbar_code"))->copyable(),
                TextColumn::make('name_kh')->label(__("global.product"))->searchable()->copyable(),
                TextColumn::make('category.name_kh')->label(__("global.cname_kh")),
                TextColumn::make('remarks_list')->label(__("global.remarkable"))
                ->state(function ($record) {
                    return $record->remarks?->pluck('name_kh')->implode(', ');
                }),
                TextColumn::make('price_list')->label(__("global.price_metric"))
                ->state(function ($record) {
                    return $record->metrics
                        ?->map(fn ($item) => $item->pivot->price != ''
                            ? "{$item->name_kh} - {$item->pivot->price} \$"
                            : $item->name_kh)
                        ->implode('<br>') ?? '';
                })
                ->html(),
                TextColumn::make('user_create.name')->label(__("global.created_by")),
                TextColumn::make('user_update.name')->label(__("global.updated_by")),
                TextColumn::make('branch.name_kh')->label(__("global.branch")),
                TextColumn::make('created_at')->label(__("global.created_at")),
                TextColumn::make('updated_at')->label(__("global.updated_at")),
            ])
            ->filters([
                static::getDataFilter(),
            ], layout: FiltersLayout::AboveContent)
            ->filtersFormWidth('4xl')
            ->filtersFormColumns(1)
            ->filtersFormMaxHeight('400px')
            ->striped()
            ->persistFiltersInSession()
            ->searchOnBlur(true)
            ->recordActions([
                ActionGroup::make([
                    EditAction::make()
                    ->label(__("global.edit")),
                    ViewAction::make()
                    ->label(__("global.detail"))
                    ->schema(function(Schema $schema){
                        return ProductDetailForm::configure($schema);
                    })
                    ->modalWidth('6xl')
                    ->modalHeading(__("global.detail"))
                    ->modalCancelAction(fn($action) => $action->color('danger'))
                    ->modalCancelActionLabel(__("global.cancel"))
                    ->modalFooterActionsAlignment(Alignment::End)
                    ->slideOver(),
                ])
                ->label('សកម្មភាព')
                // ->dropdownWidth('md')
                ->button()
                ->icon('heroicon-m-ellipsis-vertical')
                ->size(Size::Small)
            ], position: RecordActionsPosition::BeforeCells)
            ->toolbarActions([
                // BulkActionGroup::make([
                //     DeleteBulkAction::make(),
                //     ForceDeleteBulkAction::make(),
                //     RestoreBulkAction::make(),
                // ]),
            ]);
    }

    protected static function getDataFilter(): Filter
    {
        return Filter::make('filter_data')
            ->columnSpan(3)
            ->schema([
                Grid::make(4)
                    ->schema([
                        DatePicker::make('from_date')
                            ->label(__('global.from_date'))
                            ->native(false)
                            ->suffixIcon('heroicon-o-calendar')
                            ->displayFormat('d-m-Y')
                            ->placeholder('DD-MM-YYYY'),
                        DatePicker::make('to_date')
                            ->label(__('global.to_date'))
                            ->native(false)
                            ->suffixIcon('heroicon-o-calendar')
                            ->displayFormat('d-m-Y')
                            ->placeholder('DD-MM-YYYY'),
                        Select::make('main_cat_id')
                            ->label(trans('global.main_category'))
                            ->placeholder('-- ជ្រើសរើស --')
                            ->options(MainCategory::select(DB::raw('CONCAT(cat_name_kh) AS categories_name'), 'id')->branch()->pluck('categories_name', 'id')->toArray())
                            ->searchable()
                            ->live(onBlur:true)
                            ->afterStateUpdated(function ($livewire) {
                                $livewire->js('$wire.applyTableFilters()');
                            }),
                        Select::make('category_id')
                            ->label(trans('global.cname_kh'))
                            ->placeholder('-- ជ្រើសរើស --')
                            ->options(function(Get $get){
                                return $get('main_cat_id') ? Category::select(DB::raw('CONCAT(name_kh) AS name_kh'), 'id')->where('main_cat_id', $get('main_cat_id'))->branch()->pluck('name_kh', 'id')->toArray()  :[];
                            })
                            ->searchable()
                            ->live(onBlur:true)
                            ->afterStateUpdated(function ($livewire) {
                                $livewire->js('$wire.applyTableFilters()');
                            }),                        
                        TextInput::make('name_kh')
                            ->label(__('global.product')),
                        TextInput::make('pbar_code')
                            ->label(__('global.pbar_code')),

                    ]),
            ])
            ->query(function (Builder $query, array $data): Builder {
                if (! empty($data['from_date']) && ! empty($data['to_date'])) {

                    $fdate = Carbon::parse($data['from_date'])->format('Y-m-d');
                    $tdate = Carbon::parse($data['to_date'])->format('Y-m-d');
                    $column_search = 'created_at';
                    if ($fdate && $tdate) {
                        $query->where($column_search, '>=', $fdate.' 00:00:00');
                        $query->where($column_search, '<=', $tdate.' 23:59:59');
                    }
                }


                if (! empty($data['main_cat_id'])) {
                    $query->where('main_cat_id', $data['main_cat_id']);
                }

                if (! empty($data['category_id'])) {
                    $query->where('category_id', $data['category_id']);
                }

                if (! empty($data['pbar_code'])) {
                    $query->where('pbar_code', $data['approvpbar_codeed_id']);
                }

                if (! empty($data['name_kh'])) {
                    $query->where('name_kh', 'like', '%'.$data['name_kh'].'%');
                }

                return $query;
            });
    }
}
