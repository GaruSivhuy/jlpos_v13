<?php

namespace App\Filament\Resources\Control\Categories\Schemas;

use App\Models\MainCategory;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Validation\Rules\Unique;

class CategoryForm
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
                                Select::make('main_cat_id')
                                    ->label(__('global.cat_name'))
                                    ->searchable()
                                    ->required()
                                    ->live(onBlur: true)
                                    ->options(fn (): array => static::mainCategoryOptions())
                                    ->validationMessages([
                                        'required' => __('validation.required', ['attribute' => __('global.cat_name')]),
                                    ]),
                                static::nameInput('name', 'global.cname'),
                                static::nameInput('name_kh', 'global.cname_kh'),
                            ]),
                    ]),
            ]);
    }

    /**
     * Admins see the branch of each main category too, since they can see every branch.
     *
     * @return array<int, string>
     */
    protected static function mainCategoryOptions(): array
    {
        $isAdmin = auth()->user()->is_admin;

        return MainCategory::query()
            ->branch()
            ->when($isAdmin, fn ($query) => $query->with('branch'))
            ->get()
            ->mapWithKeys(fn (MainCategory $mainCategory): array => [
                $mainCategory->id => collect([
                    $mainCategory->cat_name_kh,
                    $mainCategory->cat_name,
                    $isAdmin ? $mainCategory->branch?->name_en : null,
                ])->filter()->implode(' - '),
            ])
            ->all();
    }

    protected static function nameInput(string $column, string $label): TextInput
    {
        return TextInput::make($column)
            ->required()
            ->maxLength(255)
            ->label(__($label))
            ->unique(
                table: 'categories',
                column: $column,
                ignoreRecord: true,
                modifyRuleUsing: fn (Unique $rule, Get $get) => $rule->where('main_cat_id', $get('main_cat_id')),
            )
            ->validationMessages([
                'required' => __('validation.required', ['attribute' => __($label)]),
                'unique' => __('validation.unique', ['attribute' => __($label)]),
            ]);
    }
}
