<?php

namespace App\Filament\Resources\Products\Schemas;

use App\Models\Branch;
use App\Models\Category;
use App\Models\MainCategory;
use App\Models\Metric;
use App\Models\Remark;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Unique;

class ProductForm
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
                        TextInput::make('product_id')
                            ->label(__("global.product_id"))
                            ->formatStateUsing(fn($record) => $record ? "PID-" . str_pad($record->id, env("ID_PAD_LENGTH", 4), "0", STR_PAD_LEFT) : null)
                            ->readOnly(),
                        Select::make('branch_id')
                            ->label(__("global.branch"))
                            ->searchable()
                            ->required()
                            ->live(onBlur:true)
                            ->options(auth()->user()->is_admin ? Branch::select(DB::raw('CONCAT(name_kh) AS branch_name'), 'branch.id')->pluck('branch_name', 'id')->toArray() : auth()->user()->branch()->select(DB::raw('CONCAT(name_kh) AS branch_name'), 'branch.id')->pluck('branch_name', 'id')->toArray()),
                        Select::make('main_cat_id')
                            ->label(__("global.main_category"))
                            ->searchable()
                            ->required()
                            ->live(onBlur:true)
                            ->options(MainCategory::select(DB::raw('CONCAT(cat_name_kh) AS categories_name'), 'id')->branch()->pluck('categories_name', 'id')->toArray()),
                        Select::make('category_id')
                            ->label(__("global.cname_kh"))
                            ->searchable()
                            ->live(onBlur:true)
                            ->options(function(Get $get){
                                return $get('main_cat_id') ? Category::select(DB::raw('CONCAT(name_kh) AS name_kh'), 'id')->where('main_cat_id', $get('main_cat_id'))->branch()->pluck('name_kh', 'id')->toArray()  :[];
                            })
                            ->required()
                            ->searchable()
                            ->live(onBlur:true),
                        TextInput::make('name_kh')
                        ->label(__("global.pname_kh"))
                        ->live(onBlur: true)
                        ->required()
                        ->extraAttributes([
                            'x-on:focusout' => "async () => { await \$wire.getCheckDuplicateKH(); \$wire.dispatch('advance-field-validated'); }",
                        ])
                        ->extraInputAttributes(['onchange' => 'handleAdvanceFieldBlur(this)'])
                        ->unique(
                            table: 'inventories',
                            column: 'name_kh',
                            ignoreRecord: true, // automatically excludes current record's id on edit
                            modifyRuleUsing: function (Unique $rule, Get $get) {
                                return $rule->where('branch_id', $get('branch_id'));
                            }
                        )
                        ->validationMessages([
                            'required' => __('validation.required', ['attribute' => __('global.pname_kh')]),
                            'unique'   => __('validation.unique', ['attribute' => __('global.pname_kh')]),
                        ])
                        // ->afterStateUpdated(function (Set $set, $state, Get $get, $livewire, $component) {
                        //     $statePath = $component->getStatePath();
                        //     $livewire->resetErrorBag('data.branch_id');
                        //     $livewire->resetErrorBag('data.main_cat_id');
                        //     $livewire->resetErrorBag('data.category_id');
                        //     if(empty($get('branch_id'))){
                        //         $livewire->addError(
                        //             'data.branch_id',
                        //             trans('validation.required', ['attribute' => __('global.branch')])
                        //         );
                        //         $set('name_kh', null);
                        //         return;
                        //     }

                        //     if(empty($get('main_cat_id'))){
                        //         $livewire->addError(
                        //             'data.main_cat_id',
                        //             trans('validation.required', ['attribute' => __('global.main_category')])
                        //         );
                        //         $set('name_kh', null);
                        //         return;
                        //     }

                        //     if(empty($get('category_id'))){
                        //         $livewire->addError(
                        //             'data.category_id',
                        //             trans('validation.required', ['attribute' => __('global.cname_kh')])
                        //         );
                        //         $set('name_kh', null);
                        //         return;
                        //     }

                        //     $validator = Validator::make(
                        //         ['name_kh' => $state],
                        //         [
                        //             'name_kh' => [
                        //                 'required',
                        //                 Rule::unique('inventories', 'name_kh')
                        //                     ->where('branch_id', $get('branch_id'))
                        //                     // ->ignore($get('id')),
                        //             ],
                        //         ],
                        //         [
                        //             'name_kh.required' => __('validation.required', ['attribute' => __('global.pname_kh')]),
                        //             'name_kh.unique'   => __('validation.unique', ['attribute' => __('global.pname_kh')]),
                        //         ]
                        //     );

                        //     $livewire->resetErrorBag($statePath);

                        //     if ($validator->fails()) {
                        //         $set('name_kh', null);
                        //         $livewire->addError($statePath, $validator->errors()->first('name_kh'));
                        //     }
                        // })
                        ,
                        TextInput::make('name')
                        ->label(__("global.pname"))
                        ->required()
                        ->live(onBlur: true)
                        ->extraAttributes([
                            'x-on:focusout' => "async () => { await \$wire.getCheckDuplicate(); \$wire.dispatch('advance-field-validated'); }",
                        ])
                        ->extraInputAttributes(['onchange' => 'handleAdvanceFieldBlur(this)'])
                        ->unique(
                            table: 'inventories',
                            column: 'name',
                            ignoreRecord: true, // automatically excludes current record's id on edit
                            modifyRuleUsing: function (Unique $rule, Get $get) {
                                return $rule->where('branch_id', $get('branch_id'));
                            }
                        )
                        ->validationMessages([
                            'required' => __('validation.required', ['attribute' => __('global.pname')]),
                            'unique'   => __('validation.unique', ['attribute' => __('global.pname')]),
                        ]),
                        // ->afterStateUpdated(function (Set $set, $state, Get $get, $livewire, $component) {
                        //     $statePath = $component->getStatePath();
                        //     $livewire->resetErrorBag('data.branch_id');
                        //     $livewire->resetErrorBag('data.main_cat_id');
                        //     $livewire->resetErrorBag('data.category_id');
                        //     if(empty($get('branch_id'))){
                        //         $livewire->addError(
                        //             'data.branch_id',
                        //             trans('validation.required', ['attribute' => __('global.branch')])
                        //         );
                        //         $set('name', null);
                        //         return;
                        //     }

                        //     if(empty($get('main_cat_id'))){
                        //         $livewire->addError(
                        //             'data.main_cat_id',
                        //             trans('validation.required', ['attribute' => __('global.main_category')])
                        //         );
                        //         $set('name', null);
                        //         return;
                        //     }

                        //     if(empty($get('category_id'))){
                        //         $livewire->addError(
                        //             'data.category_id',
                        //             trans('validation.required', ['attribute' => __('global.cname_kh')])
                        //         );
                        //         $set('name', null);
                        //         return;
                        //     }

                        //     $validator = Validator::make(
                        //         ['name' => $state],
                        //         [
                        //             'name' => [
                        //                 'required',
                        //                 Rule::unique('inventories', 'name')
                        //                     ->where('branch_id', $get('branch_id'))
                        //                     ->ignore($get('id')),
                        //             ],
                        //         ],
                        //         [
                        //             'name.required' => __('validation.required', ['attribute' => __('global.pname')]),
                        //             'name.unique'   => __('validation.unique', ['attribute' => __('global.pname')]),
                        //         ]
                        //     );

                        //     $livewire->resetErrorBag($statePath);

                        //     if ($validator->fails()) {
                        //         $set('name', null);
                        //         $livewire->addError($statePath, $validator->errors()->first('name'));
                        //     }
                        // }),
                        Select::make('metrics')
                            ->label(__('global.mname'))
                            ->relationship(
                                name: 'metrics',
                                titleAttribute: 'name_kh',
                                modifyQueryUsing: fn ($query) => $query->branch(),
                            )
                            ->getOptionLabelFromRecordUsing(function (Metric $record) {
                                if (auth()->user()->is_admin) {
                                    $branchName = DB::table('branch')->where('id', $record->branch_id)->value('name_kh');
                                    return "{$record->name_kh} ({$branchName})";
                                }

                                return $record->name_kh;
                            })
                            ->multiple()
                            ->searchable()
                            ->required()
                            ->placeholder(__('global.select') . ' ' . __('global.mname')),
                        TextInput::make('pbar_code')
                            ->label(__("global.pbar_code")),
                        Select::make('remarks')
                            ->label(__('global.remarkable'))
                            ->relationship(
                                name: 'remarks',
                                titleAttribute: 'name_kh',
                                modifyQueryUsing: fn ($query) => $query->branch(),
                            )
                            ->getOptionLabelFromRecordUsing(function (Remark $record) {
                                if (auth()->user()->is_admin) {
                                    $branchName = DB::table('branch')->where('id', $record->branch_id)->value('name_kh');
                                    return "{$record->name_kh} ({$branchName})";
                                }

                                return $record->name_kh;
                            })
                            ->multiple()
                            ->searchable()
                            ->placeholder(__('global.select') . ' ' . __('global.remarkable')),
                        Textarea::make('description')
                            ->label(__("global.description"))
                            ->rows(5),
                    ]),
                    Grid::make()
                    ->columns(1)
                    ->columnSpan(2)
                    ->schema([
                        SpatieMediaLibraryFileUpload::make('product')
                        ->label(__("global.attachment"))
                        ->collection('product')
                        ->image()
                        ->disk('products')
                        ->conversionsDisk('products')
                        ->imagePreviewHeight(200)
                        ->panelAspectRatio('2:1')
                        ->acceptedFileTypes(['image/jpeg', 'image/jpg', 'image/png'])
                        ->maxSize(1024)
                        ->imageEditor()
                        ->imageEditorAspectRatioOptions([null, '16:9', '4:3', '1:1'])
                        ->getUploadedFileNameForStorageUsing(function ($file) {
                            $extension = $file->getClientOriginalExtension();

                            return 'product_photo_'.'-'.date('Y-m-d').'-'.time().'.'.$extension;
                        })
                    ])
                ])
            ]);
    }
}
